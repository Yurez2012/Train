@extends('layout.app')

@section('content')
    <table class="table-auto border-separate border-spacing-2 border border-gray-400 dark:border-gray-500">
        <thead>
            <tr>
                <td class="px-4 border border-gray-300 dark:border-gray-600">
                    Station
                </td>
                <td class="px-4 border border-gray-300 dark:border-gray-600">
                    Arrive
                </td>
                <td class="px-4 border border-gray-300 dark:border-gray-600">
                    Departure
                </td>
                <td class="px-4 border border-gray-300 dark:border-gray-600">
                    Delay
                </td>
            </tr>
        </thead>
        <tbody>
            @php $count = 0; @endphp

            @foreach($result as  $key => $item)
                <tr>
                    <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                        {{ $key }}
                    </td>
                    <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                        {{ $item['arrives']['date'] ?? '' }}
                    </td>
                    <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                        {{ $item['departure']['date'] ?? '' }}
                    </td>
                    <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                        @if(isset($item['arrives']['date']) && isset($item['departure']['date']))
                            @php $count +=  Carbon\Carbon::parse($item['arrives']['date'])->diffInMinutes($item['departure']['date']) @endphp
                            {{ Carbon\Carbon::parse($item['arrives']['date'])->diffInMinutes($item['departure']['date']) }} Minutes
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                    Total delay
                </td>
                <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">

                </td>
                <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">

                </td>
                <td class="px-4 whitespace-nowrap border border-gray-300 dark:border-gray-600">
                    {{ $count }} Minutes
                </td>
            </tr>
        </tbody>
    </table>


@endsection
