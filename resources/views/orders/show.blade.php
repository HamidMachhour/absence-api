<x-layout>

    <x-card class="flex flex-col flex-wrap justify-center bg-orange-200 mb-2">

        <x-card class="lg:grid lg:grid-cols-9 gap-1 text-white text-xs ">
            <div class="flex justify-center w-full bg-orange-600   p-1">
                Absence Id
            </div>

            <div class="flex justify-center w-full bg-orange-600   p-1">
                Created At
            </div>
            <div class="flex justify-center w-full bg-orange-600   p-1">
                Updated At
            </div>
            <div class="flex justify-center w-full bg-orange-600   p-1">
                NB Items
            </div>
            <div class="flex justify-center w-full bg-orange-600   p-1">
                Price
            </div>
            <div class="flex justify-center w-full bg-orange-600   p-1">
                Latest Status
            </div>
            <div class="flex justify-center w-full bg-orange-600  col-span-3 p-1">
                Actions
            </div>
        </x-card>

        <x-absence-card :absence="$absence" />
        <x-card class="lg:grid lg:grid-cols-10 gap-1 text-white text-xs ">
            <div class="flex items-center justify-center w-full border-1 border-gray-600 text-gray-600   p-1">
                Payment Method
            </div>
            <div class="flex items-center justify-center w-full bg-green-600   p-1">
                {{ $absence->paymentMethod == null ? '' : $absence->paymentMethod->name }}
            </div>
            <div class="flex items-center justify-center w-full border-1 border-gray-600 text-gray-600   p-1">
                Delivery Method
            </div>

            <div class="flex items-center justify-center w-full bg-green-600   p-1">
                {{ $absence->matiere == null ? '' : $absence->matiere->name }}
            </div>
            <div class="flex items-center justify-center w-full border-1 border-gray-600 text-gray-600   p-1">
                Delivery Address
            </div>
            <div class="flex items-center justify-center w-full bg-green-600  col-span-2 p-1">
                {{ $absence->deliveryAddress == null ? '' : $absence->deliveryAddress->toString() }}
            </div>
            <div class="flex items-center justify-center w-full border-1 border-gray-600 text-gray-600   p-1">
                Billing Address
            </div>
            <div class="flex items-center justify-center w-full bg-green-600 col-span-2   p-1">
                {{ $absence->billingAddress == null ? '' : $absence->billingAddress->toString() }}
            </div>
        </x-card>
    </x-card>

    <div class="lg:grid lg:grid-cols-1 gap-1">

        @unless (count($absence->absenceStudents) == 0)

            <div class="lg:grid lg:grid-cols-11 gap-1">
                <div class="flex justify-center bg-orange-600 text-white p-1 text-xs col-span-6">
                    Student
                </div>
                <div class="flex justify-center bg-orange-600 text-white p-1  text-xs">
                    Price
                </div>
                <div class="flex justify-center bg-orange-600 text-white p-1 text-xs">
                    Fee
                </div>
                <div class="flex justify-center bg-orange-600 text-white p-1 text-xs">
                    Heures
                </div>

                <div class="flex justify-center bg-orange-600 text-white p-1 text-xs">
                    Discount
                </div>
                <div class="flex justify-center bg-orange-600 text-white p-1 text-xs">
                    Subtotal
                </div>
                <div class="text-xs">
                </div>
            </div>

            @foreach ($absence->absenceStudents as $absence_student)
                <x-orderline-card :orderline="$absence_student" />
            @endforeach
        @else
            <p>This absence is empty</p>
        @endunless

    </div>

</x-layout>
