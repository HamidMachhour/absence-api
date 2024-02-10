<x-layout>
  <div class="mx-4">
    <x-card class="p-10">
        @auth
        <div class="container gap-2">
            <div class="row">
              <div class="col-sm-2">
                <div class="container ">
                    <div class="row mb-2">
                        <a href="{{ route('addresses.index') }}" class="bg-orange-600 text-white hover:text-green-700 p-2 rounded-lg">
                            <i class="fa-solid fa-shopping-basket"></i>
                            </a>
                    </div>

                    <div class="row mb-2">
                        <a href="#" class="bg-green-600 text-white hover:text-green-700 p-2 rounded-lg">
                            <i class="fa-solid fa-shopping-basket"></i>
                            Orders</a>
                    </div>

                    <div class="row mb-2">
                        <a href="#" class="bg-green-600 text-white hover:text-green-700 p-2 rounded-lg">
                            <i class="fa-solid fa-shopping-basket"></i>
                            Sells</a>
                    </div>

                    <div class="row mb-2">
                        <a href="#" class="bg-green-600 text-white hover:text-green-700 p-2 rounded-lg">
                            <i class="fa-solid fa-shopping-basket"></i>
                            Inventory</a>
                    </div>
                </div>
              </div>
              <div class="col-sm-8 bg-orange-200">
                One of three columns
              </div>
              <div class="col-sm-2 bg-green-200">
                One of three columns
              </div>
            </div>
          </div>
        @endauth
    </x-card>
  </div>
</x-layout>
