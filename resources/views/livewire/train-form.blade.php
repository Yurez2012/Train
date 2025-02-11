<div class="mt-3">
    <div class="flex flex-row gap-5 items-center">
        <p class="w-full">
            Station:
        </p>
        <p class="w-full">
            Date:
        </p>
        <p class="w-full">
            Time:
        </p>
        <p class="w-full">
            Train ID:
        </p>
        <p class="w-[100px] h-[40px] p-2">
            Add
        </p>
    </div>
    <form action="{{ route('/') }}" method="GET">
        @csrf

        @foreach ($trains as $index => $train)
            <div class="mb-2 flex flex-row gap-5 items-center">
                <div class="w-full">
                    <input value="{{ Arr::get($train, 'station') }}" class="w-full p-2 border border-gray-300 rounded" type="text" name="trains[{{ $index }}][station]" placeholder="Station"/>
                </div>
                <div class="w-full">
                    <input value="{{ Arr::get($train, 'date') }}" class="w-full p-2 border border-gray-300 rounded" type="date" name="trains[{{ $index }}][date]"/>
                </div>
                <div class="w-full">
                    <input value="{{ Arr::get($train, 'time') }}" class="w-full p-2 border border-gray-300 rounded" type="text" name="trains[{{ $index }}][time]" placeholder="Time fomat HH(08, 14...)"/>
                </div>
                <div class="w-full">
                    <input value="{{ Arr::get($train, 'trainID') }}" class="w-full p-2 border border-gray-300 rounded" type="text" name="trains[{{ $index }}][trainID]" placeholder="Train id"/>
                </div>
                <button wire:click="addTrain" type="button"
                        class="p-2 w-[120px] h-[40px] bg-blue-500 text-white border-none rounded cursor-pointer">+
                </button>
            </div>
        @endforeach
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 text-white border-none rounded cursor-pointer px-6 py-2">
                Get information
            </button>
        </div>
    </form>

</div>

