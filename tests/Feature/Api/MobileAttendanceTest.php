<?php

namespace Tests\Feature\Api;

use App\Models\Attendance;
use App\Models\AttendanceStudent;
use App\Models\Absence;
use App\Models\AbsenceStudent;
use App\Models\Classe;
use App\Models\Role;
use App\Models\Seance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Models\UserRole;
use App\Models\_Day;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private function createTeacher(string $email): User
    {
        $teacher = User::create([
            'firstName' => 'Teacher',
            'lastName' => 'One',
            'firstNameEn' => 'Teacher',
            'lastNameEn' => 'One',
            'email' => $email,
            'password' => bcrypt('password'),
        ]);

        $teacherRole = Role::firstOrCreate(
            ['nameEn' => 'teacher'],
            ['name' => 'teacher', 'nameFr' => 'teacher', 'nameZ' => 'teacher']
        );

        UserRole::firstOrCreate([
            'userId' => $teacher->id,
            'roleId' => $teacherRole->id,
        ]);

        return $teacher;
    }

    private function createRosterContext(): array
    {
        $teacher = $this->createTeacher('teacher@example.test');

        $classe = Classe::create([
            'name' => 'DEV-101',
            'nameEn' => 'DEV-101',
            'userId' => $teacher->id,
        ]);

        $subject = Subject::create([
            'name' => 'PHP Programming',
            'nameEn' => 'PHP Programming',
        ]);

        $day = _Day::firstOrCreate([
            'name' => 'Sunday',
            'nameEn' => 'Sunday',
        ]);

        $seance = Seance::create([
            'teacherId' => $teacher->id,
            'classeId' => $classe->id,
            'subjectId' => $subject->id,
            'dayId' => $day->id,
            'salle' => '102',
            'de' => Carbon::now()->subHour()->format('H:i:s'),
            'a' => Carbon::now()->addHour()->format('H:i:s'),
        ]);

        $studentUsers = collect([
            ['email' => 'student1@example.test', 'first' => 'Youssef', 'last' => 'Amrani'],
            ['email' => 'student2@example.test', 'first' => 'Amina', 'last' => 'Mansouri'],
        ])->map(function (array $data) {
            return User::create([
                'firstName' => $data['first'],
                'lastName' => $data['last'],
                'firstNameEn' => $data['first'],
                'lastNameEn' => $data['last'],
                'email' => $data['email'],
                'password' => bcrypt('password'),
            ]);
        });

        $students = collect([
            Student::create([
                'num' => 1,
                'userId' => $studentUsers[0]->id,
                'classeId' => $classe->id,
            ]),
            Student::create([
                'num' => 2,
                'userId' => $studentUsers[1]->id,
                'classeId' => $classe->id,
            ]),
        ]);

        return [
            'teacher' => $teacher,
            'seance' => $seance,
            'students' => $students,
        ];
    }

    public function test_authorized_teacher_can_fetch_roster(): void
    {
        $context = $this->createRosterContext();
        Sanctum::actingAs($context['teacher']);

        $response = $this->getJson('/api/v1/seances/' . $context['seance']->id . '/roster');

        $response
            ->assertOk()
            ->assertJsonPath('seance_id', $context['seance']->id)
            ->assertJsonPath('classe_name', 'DEV-101')
            ->assertJsonPath('subject', 'PHP Programming')
            ->assertJsonCount(2, 'students');
    }

    public function test_authorized_teacher_can_fetch_own_seances_with_formatted_ranges(): void
    {
        $context = $this->createRosterContext();
        Sanctum::actingAs($context['teacher']);

        Seance::create([
            'teacherId' => $context['teacher']->id,
            'classeId' => $context['seance']->classeId,
            'subjectId' => $context['seance']->subjectId,
            'dayId' => $context['seance']->dayId,
            'salle' => '205',
            'de' => '13:00:00',
            'a' => '15:00:00',
        ]);

        $response = $this->getJson('/api/v1/teachers/me/seances');

        $response
            ->assertOk()
            ->assertJsonPath('teacher_id', $context['teacher']->id)
            ->assertJsonCount(2, 'seances');

        $ranges = collect($response->json('seances'))->pluck('time_range')->all();
        $shortRanges = collect($response->json('seances'))->pluck('time_range_short')->all();

        $this->assertContains('13:00 - 15:00', $ranges);
        $this->assertContains('13 - 15', $shortRanges);
    }

    public function test_authorized_teacher_can_submit_absences_batch(): void
    {
        $context = $this->createRosterContext();
        Sanctum::actingAs($context['teacher']);

        $payload = [
            'absent_student_ids' => [$context['students'][0]->id],
            'timestamp' => Carbon::now()->subMinutes(5)->toDateTimeString(),
        ];

        $response = $this->postJson('/api/v1/seances/' . $context['seance']->id . '/attendance', $payload);

        $response
            ->assertOk()
            ->assertJsonPath('seance_id', $context['seance']->id)
            ->assertJsonPath('absence_created', true);

        $absence = Absence::where('seanceId', $context['seance']->id)->first();
        $this->assertNotNull($absence);

        $this->assertDatabaseHas('AbsenceStudent', [
            'absenceId' => $absence->id,
            'studentId' => $context['students'][0]->id,
        ]);

        $this->assertNull(Attendance::where('seanceId', $context['seance']->id)->first());
    }

    public function test_unauthenticated_requests_are_rejected_and_unauthorized_teacher_requests_are_forbidden(): void
    {
        $context = $this->createRosterContext();

        $this->getJson('/api/v1/seances/' . $context['seance']->id . '/roster')->assertUnauthorized();

        $otherTeacher = $this->createTeacher('other-teacher@example.test');
        Sanctum::actingAs($otherTeacher);

        $this->getJson('/api/v1/seances/' . $context['seance']->id . '/roster')->assertForbidden();
        $this->postJson('/api/v1/seances/' . $context['seance']->id . '/attendance', [
            'absent_student_ids' => [$context['students'][0]->id],
            'timestamp' => Carbon::now()->toDateTimeString(),
        ])->assertForbidden();

        $this->assertDatabaseCount('AttendanceStudent', 0);
    }

    public function test_roster_for_missing_or_foreign_seance_returns_json_error(): void
    {
        $context = $this->createRosterContext();
        $otherTeacher = $this->createTeacher('other-teacher@example.test');
        Sanctum::actingAs($otherTeacher);

        $this->getJson('/api/v1/seances/' . $context['seance']->id . '/roster')
            ->assertStatus(403)
            ->assertJsonStructure(['message']);

        $this->getJson('/api/v1/seances/does-not-exist/roster')
            ->assertStatus(404)
            ->assertJsonStructure(['message']);
    }
}
