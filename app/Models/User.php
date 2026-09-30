<?php

namespace App\Models;

use App\Models\Classe;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


/**
 * Class User
 *
 * @property string|null $id
 * @property string|null $firstName
 * @property string|null $lastName
 * @property string|null $code
 * @property string|null $email
 * @property DateTime $email_verified_at
 * @property string|null $password
 * @property string $remember_token
 * @property string $deleted_at
 * @property string $addressId
 * @property string $imageId
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $table = '_User';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'firstNameFr',
        'lastNameFr',
        'firstNameEn',
        'lastNameEn',
        'firstNameZ',
        'lastNameZ',

        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'addressId',
        'imageId'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });

        static::deleting(function ($user) {
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($user) {
                $user->seances()->delete();
                $user->classes->each->delete(); // This will trigger cascading deletes on Seance, Student, etc.
                $user->students->each->delete();
                $user->addresses()->delete();
                $user->userImages()->delete();
                $user->roles()->delete();
                $user->enrollments()->delete();
                // Handle both sides of the friendship
                Friendship::where('userId', $user->id)->orWhere('friend', $user->id)->delete();
                // Handle both sent and received messages
                Message::where('s_user_id', $user->id)->orWhere('d_user_id', $user->id)->delete();
            });
        });
    
    }


    public function scopeFilter($query, Request $request)
    {
        $query->when($request->filled('id'), function ($q) use ($request) {
            $q->where('id', $request->id);
        });

        $query->when($request->filled('roleId'), function ($q) use ($request) {
            $q->whereHas('roles', function ($roles) use ($request) {
                $roles->where('roleId', $request->roleId);
            });
        });

        // New: filter by multiple roles from checkboxes
        $query->when($request->filled('role'), function ($q) use ($request) {
            $roles = $request->role; // e.g. ['student','teacher']

            $q->whereHas('roles', function ($userRolesQuery) use ($roles) {
                $userRolesQuery->whereHas('role', function ($roleQuery) use ($roles) {
                    $roleQuery->whereIn('nameEn', $roles);
                });
            });
        });

        $query->when($request->filled('verified'), function ($q) use ($request) {
            if ($request->verified === 'yes')
                $q->whereNotNull('email_verified_at');
            else
                $q->whereNull('email_verified_at');
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('firstName', 'like', '%' . $request->search . '%')
                ->orWhere('lastName', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('code', 'like', '%' . $request->search . '%');
        });

        // New: sorting
        $query->when($request->filled('sort'), function ($q) use ($request) {
            if ($request->sort === 'name_asc')
                $q->orderBy('firstName', 'asc')->orderBy('lastName', 'asc');
            elseif ($request->sort === 'name_desc')
                $q->orderBy('firstName', 'desc')->orderBy('lastName', 'desc');
            elseif ($request->sort === 'newest')
                $q->orderBy('created_at', 'desc');
            elseif ($request->sort === 'oldest')
                $q->orderBy('created_at', 'asc');
        });

        $query->when($request->filled('friend_status'), function ($q) use ($request) {

            $friend_status = $request->friend_status;
            $currentUserId = Auth::id();

            if (!$currentUserId) {
                return $q;
            }

            if ($friend_status === Friendship::STATUS_PENDING) {
                $q->whereHas('friendships', function ($sub) use ($currentUserId, $friend_status) {
                    $sub->where('status', $friend_status)
                        ->where('friend', $currentUserId); // The logged-in user is the RECIPIENT
                });
            }
            // --- 1. Filtering Users who HAVE a relationship ---
            if (in_array($friend_status, [
                Friendship::STATUS_ACCEPTED,
                Friendship::STATUS_REJECTED,
                Friendship::STATUS_BLOCKED
            ])) {
                // Logic to check if the user being queried ($q) has a friendship record with $currentUserId

                // 1. Check if queried user is the SENDER:
                $q->whereHas('friendships', function ($sub) use ($currentUserId, $friend_status) {
                    $sub->where('status', $friend_status)
                        ->where('friend', $currentUserId); // The logged-in user is the RECIPIENT
                })
                    // 2. OR check if queried user is the RECIPIENT:
                    ->orWhereHas('friendships', function ($sub) use ($currentUserId, $friend_status) {
                        $sub->where('status', $friend_status)
                            ->where('userId', $currentUserId); // The logged-in user is the SENDER
                    });
            }
            if ($friend_status === 'not_friends') {

                // 1. Find the IDs of ALL users who have *any* relationship (any status, any direction) 
                //    with the current user.
                $relatedIds = Friendship::query()
                    ->where(function ($sub) use ($currentUserId) {
                        $sub->where('userId', $currentUserId)
                            ->orWhere('friend', $currentUserId);
                    })
                    ->pluck('userId', 'friend')
                    ->flatten() // Converts the associative array of IDs into a single list
                    ->unique()
                    ->all();


                // 2. Filter out all users found in $relatedIds. The current user is excluded 
                //    by the global scope outside this block.
                $q->whereNotIn('id', $relatedIds);
            }
        });

        return $query->where('id', '!=', Auth::id());
    }

    public function fullName()
    {

        $locale = Session::get('locale', 'en'); // Get the locale from the session, with 'en' as the default
        if ($locale === 'ar') {
            return $this->firstName . ' ' . $this->lastName;
        } elseif ($locale === 'fr') {
            return $this->firstNameFr . ' ' . $this->lastNameFr;
        } elseif ($locale === 'en') {
            return $this->firstNameEn . ' ' . $this->lastNameEn;
        }
        return $this->firstNameZ . ' ' . $this->lastNameZ;
    }

    // Relationship With Seance
    public function seances()
    {
        return $this->hasMany(Seance::class, 'teacherId');
    }

    // Relationship with Friendship
    public function friendships()
    {
        return $this->hasMany(Friendship::class, 'userId');
    }


    public function sourceMessages()
    {
        return $this->hasMany(Message::class, 's_user_id');
    }

    public function targetMessages()
    {
        return $this->hasMany(Message::class, 'd_user_id');
    }

    //accepted friends users only
    public function friends()
    {
        // 1. Get all Friendship records for accepted status
        $currentUserId = $this->id;
        $friendships = Friendship::where('status', Friendship::STATUS_ACCEPTED)
            // Wrap the symmetric check (userId=sender OR friend=recipient) globally
            ->where(function ($query) use ($currentUserId) {
                $query->where('userId', $currentUserId)
                    ->orWhere('friend', $currentUserId);
            })
            ->get();

        // 2. Extract the IDs of the OTHER user in the relationship
        $friendIds = $friendships->map(function ($friendship) {
            // If I am the sender, the friend is the recipient
            if ($friendship->userId === $this->id) {
                return $friendship->friend;
            }
            // If I am the recipient, the friend is the sender
            return $friendship->userId;
        })->unique()->filter(); // Filter out nulls/zeros if any

        // 3. Return the User models corresponding to those IDs
        return User::whereIn('id', $friendIds)->get();
    }

    // is friend with user

    public function isFriendWith($userId)
    {
        $currentUserId = $this->id;

        $exist = Friendship::where('status', Friendship::STATUS_ACCEPTED)

            // Wrap the entire symmetric check in a single group
            ->where(function ($symmetricQuery) use ($currentUserId, $userId) {

                // 1. Current user is sender
                $symmetricQuery->where(function ($query) use ($currentUserId, $userId) {
                    $query->where('userId', $currentUserId)->where('friend', $userId);
                })

                    // 2. OR Current user is recipient
                    ->orWhere(function ($query) use ($currentUserId, $userId) {
                        $query->where('userId', $userId)->where('friend', $currentUserId);
                    });
            })
            ->exists();

        return $exist;
    }

    // is blocked with user
    public function hasBlockedFriendship($userId)
    {
        return Friendship::where('status', Friendship::STATUS_BLOCKED)
            // Group the OR conditions for symmetry
            ->where(function ($query) use ($userId) {
                // Check current user is sender AND target is recipient
                $query->where('userId', $this->id)->where('friend', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                // OR check target user is sender AND current user is recipient
                $query->where('userId', $userId)->where('friend', $this->id);
            })
            ->exists();
    }
    // You can apply the exact same structure to hasRejectedFriendship, changing the status.

    //is rejected with user
    public function hasRejectedFriendship($userId)
    {
        // Use the base Friendship model to check both sides explicitly
        return Friendship::where('status', Friendship::STATUS_REJECTED)
            ->where(function ($query) use ($userId) {
                // Relationship involves current user ($this->id) and target user ($userId)
                $query->where(function ($sub) use ($userId) {
                    $sub->where('userId', $this->id)->where('friend', $userId);
                })->orWhere(function ($sub) use ($userId) {
                    $sub->where('userId', $userId)->where('friend', $this->id);
                });
            })
            ->exists();
    }

    // has pending friend request from user
    public function hasPendingFriendRequestFrom($userId)
    {
        return Friendship::where('status', Friendship::STATUS_PENDING)
            ->where('userId', $userId) // The sender is the target user
            ->where('friend', $this->id) // The recipient is the current user
            ->exists();
    }
    // has pending friend request to user
    public function hasPendingFriendRequestTo($userId)
    {
        return Friendship::where('status', Friendship::STATUS_PENDING)
            ->where('userId', $this->id) // The sender is the current user
            ->where('friend', $userId) // The recipient is the target user
            ->exists();
    }
    // Relationship with AbsenceStudent
    public function absenceStudents()
    {
        return AbsenceStudent::whereHas('student', function ($studentQuery) {
            $studentQuery->where('userId', $this->id);
        });
    }
    // Relationship With Classe
    public function classes()
    {
        return $this->hasMany(Classe::class, 'userId');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'userId');
    }
    public function addresses()
    {
        return $this->hasMany(Address::class, 'userId');
    }

    public function latestClasse()
    {
        // Call latest() on the relationship query builder, then get the first result
        $latestStudent = $this->students()->latest()->first();

        // Check if a student was found and return their classeId
        if ($latestStudent) {
            return $latestStudent->classeId;
        }

        // If no students were found, return null
        return null;
    }
    public function address()
    {
        return $this->belongsTo(Address::class, 'addressId');
    }

    public function userImage()
    {
        return $this->belongsTo(UserImage::class, 'imageId');
    }

    public function userImages()
    {
        return $this->hasMany(UserImage::class, 'userId');
    }
    public function roles()
    {
        return $this->hasMany(UserRole::class, 'userId');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'userId');
    }


    public function localRole()
    {
        $user_role = UserRole::where('userId', $this->id)->first();
        if ($user_role)
            return $user_role->role->roleName();
        else
            return '';
    }

    public function role()
    {
        $user_role = UserRole::where('userId', $this->id)->first();
        if ($user_role)
            return $user_role->role->nameEn;
        else
            return 'guest';
    }

    public function isTeacher()
    {
        return ($this->hasRole('teacher') ||
            Seance::where('teacherId', $this->id)->exists());
    }

    /**
     * Get all users who are teachers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function teachers()
    {
        // Get the unique IDs of all professors from the seances table.
        $seanceProfIds = Seance::select('teacherId')->distinct()->pluck('teacherId');

        // Find all users who either have the 'teacher' role or
        // whose ID appears as a professor in the seances table.
        return self::whereHas('roles.role', function ($query) {
            $query->where('nameEn', 'teacher');
        })->orWhereIn('id', $seanceProfIds)->get();
    }


    public function isStudentCandidate()
    {
        $exists = Student::where('userId', $this->id)->exists();
        return ($this->hasRole('student') && !$exists);
    }

    public function isStudent()
    {
        return Student::where('userId', $this->id)->exists();
    }


    public function hasRole($role_name)
    {
        $exists = UserRole::where('userId', $this->id)
            ->whereRelation('role', 'nameEn', $role_name)
            ->exists();
        return $exists;
    }


    public static function withRole($role_name)
    {
        $users = User::whereHas(
            'roles',
            function ($roles) use ($role_name) {
                $roles->whereHas(
                    'role',
                    function ($role) use ($role_name) {
                        $role->where('nameEn', $role_name);
                    }
                );
            }
        );

        return $users;
    }

    public function unreadMessages()
    {
        return Message::where('d_user_id', $this->id)
            ->where('status', Message::STATUS_UNREAD)
            ->get();
    }
    public function nbUnreadMessages()
    {
        return $this->unreadMessages()
            ->count();
    }

    public function nbMyAdresses()
    {
        return $this->addresses->count();
    }

    public function nbMyAbsences()
    {
        return $this->absenceStudents()->count();
    }

    public  function nbMyClasses()
    {
        return $this->classes->count();
    }

    public function nbMyEnrollments()
    {
        return $this->enrollments()->count();
    }

    public static function nbEnrollments()
    {
        return Enrollment::all()->count();
    }

    public static function nbUsers()
    {
        return User::all()->count();
    }

    public static function nbClasses()
    {
        return Classe::all()->count();
    }

    public static function nbSubjects()
    {
        return Subject::all()->count();
    }

    public static function nbRoles()
    {
        return Role::all()->count();
    }
    public static function latestAbsencesCount()
    {

        $request = request();

        $count = AbsenceStudent::when($request->filled('days'), function ($q) use ($request) {

            $q->where('created_at', '>', now()->subDays($request->days)->endOfDay());
        })->count();

        return $count;
    }

    public static function myLatestAbsencesCount()
    {

        $request = request();
        $currentUser = auth()->user();

        $count = AbsenceStudent::whereIn('studentId', function ($query) use ($currentUser) {
            $query->select('id')
                ->from('Student')
                ->where('userId', $currentUser->id);
        })
            ->when($request->filled('days'), function ($q) use ($request) {
                $q->where('created_at', '>', now()->subDays($request->days)->endOfDay());
            })
            ->count();

        return $count;
    }

    public static function nbLatestRapports($days)
    {
        return 0;
    }
}
