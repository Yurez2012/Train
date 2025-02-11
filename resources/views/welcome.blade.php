@extends('layout.app')

@section('content')
    <div class="container mx-auto px-4 py-10">
        <h1>
            Trains script
        </h1>

        <livewire:train-form/>

        @if(count($result) > 0)
            <table class="table-auto border-separate border-spacing-2 border border-gray-400 dark:border-gray-500">
                <thead>
                <tr>
                    <td class="px-4 border border-gray-300 dark:border-gray-600">
                        Station
                    </td>
                    <td class="px-4 border border-gray-300 dark:border-gray-600">
                        Planned Departure
                    </td>
                    <td class="px-4 border border-gray-300 dark:border-gray-600">
                        Actual Departure
                    </td>
                    <td class="px-4 border border-gray-300 dark:border-gray-600">
                        Delay Departure
                    </td>
                    <td class="px-4 border border-gray-300 dark:border-gray-600">
                        Planned Arrive
                    </td>
                    <td class="px-4 border border-gray-300 dark:border-gray-600">
                        Actual Arrive
                    </td>
                    <td class="px-4 border border-gray-300 dark:border-gray-600">
                        Delay Arrive
                    </td>
                </tr>
                </thead>
                <tbody>
                @php $countDepar = 0; @endphp
                @php $countArrive = 0; @endphp

                @foreach($result as  $key => $item)
                    <tr>
                        <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                            {{ $key }}
                        </td>
                        <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                            {{ $item['departure']['date'] ?? '' }}
                        </td>
                        <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                            {{ $item['departure']['dp_ct'] ?? '' }}
                        </td>
                        <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                            @if(isset($item['departure']['date']) && isset($item['departure']['dp_ct']))
                                @php $countDepar +=  Carbon\Carbon::parse($item['departure']['date'])->diffInMinutes($item['departure']['dp_ct']) @endphp
                                {{ Carbon\Carbon::parse($item['departure']['date'])->diffInMinutes($item['departure']['dp_ct']) }}
                                Minutes
                            @endif
                        </td>
                        <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                            {{ $item['arrives']['date'] ?? '' }}
                        </td>
                        <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                            {{ $item['arrives']['dp_ct'] ?? '' }}
                        </td>
                        <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                            @if(isset($item['arrives']['date']) && isset($item['arrives']['dp_ct']))
                                @php $countArrive +=  Carbon\Carbon::parse($item['arrives']['date'])->diffInMinutes($item['arrives']['dp_ct']) @endphp
                                {{ Carbon\Carbon::parse($item['arrives']['date'])->diffInMinutes($item['arrives']['dp_ct']) }}
                                Minutes
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                        Delay
                    </td>
                    <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">

                    </td>
                    <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">

                    </td>
                    <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                        {{ $countDepar }} Minutes
                    </td>
                    <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">

                    </td>
                    <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">

                    </td>
                    <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                        {{ $countArrive }} Minutes
                    </td>
                </tr>
                </tbody>
            </table>
        @endif
    </div>
@endsection
