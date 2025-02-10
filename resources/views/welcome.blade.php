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
                            {{ Carbon\Carbon::parse($item['arrives']['date'])->diffInMinutes($item['departure']['date']) }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
