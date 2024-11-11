<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $report->getTitle() }}</title>
    <style>
        .header {
            color: #374151;
            margin-bottom: 1rem;
        }

        .header > * + * {
            margin-top: 0.5rem;
        }

        .table-head {
            display: table-row-group;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .table-class th,
        .table-class td {
            color: #374151;
        }

        .whitespace-normal {
            white-space: normal;
        }

        .whitespace-nowrap {
            white-space: nowrap;
        }

        .title {
            font-size: 1.5rem;
        }

        .company-name {
            font-size: 1.125rem;
            font-weight: bold;
        }

        .date-range {
            font-size: 0.875rem;
        }

        .table-class {
            width: 100%;
            border-collapse: collapse;
        }

        .table-class th,
        .table-class td {
            padding: 0.75rem;
            font-size: 0.75rem;
            line-height: 1rem;
            border-bottom: 1px solid #d1d5db; /* Gray border for all rows */
        }

        .category-header-row > td,
        .type-header-row > td {
            background-color: #f3f4f6; /* Gray background for category names */
            font-weight: bold;
        }

        .type-header-row > td,
        .type-data-row > td,
        .type-summary-row > td {
            padding-left: 1.5rem; /* Indentation for type rows */
        }

        .table-body tr {
            background-color: #ffffff; /* White background for other rows */
        }

        .spacer-row > td {
            height: 0.75rem;
        }

        .bold {
            font-weight: bold;
        }

        .category-summary-row > td,
        .type-summary-row > td,
        .table-footer-row > td {
            font-weight: bold;
            background-color: #ffffff; /* White background for footer */
        }

        .underline-thin::after {
            content: '';
            display: block;
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #374151; /* Adjust as needed */
        }

        .underline-bold::after {
            content: '';
            display: block;
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px; /* Adjust as needed */
            background-color: #374151; /* Adjust as needed */
        }

        /* Ensure td is relatively positioned to contain the absolute underline */
        .cell {
            position: relative;
            padding-bottom: 5px; /* Adjust padding to add space for the underline */
        }
    </style>
</head>
<body>
<div class="header">
    <div class="title">{{ $report->getTitle() }}</div>
    <div class="company-name">{{ $company->name }}</div>
    @if($startDate && $endDate)
        <div class="date-range">Date Range: {{ $startDate }} to {{ $endDate }}</div>
    @else
        <div class="date-range">As of {{ $endDate }}</div>
    @endif
</div>
<table class="table-class">
    <thead class="table-head">
    <tr>
        @foreach($report->getCashInflowAndOutflowHeaders() as $index => $header)
            <th class="{{ $report->getAlignmentClass($index) }}">
                {{ $header }}
            </th>
        @endforeach
    </tr>
    </thead>
    @foreach($report->getCategories() as $category)
        <tbody>
        <tr class="category-header-row">
            @foreach($category->header as $index => $header)
                <td class="{{ $report->getAlignmentClass($index) }}">
                    {{ $header }}
                </td>
            @endforeach
        </tr>
        @foreach($category->data as $account)
            <tr>
                @foreach($account as $index => $cell)
                    <td class="{{ $report->getAlignmentClass($index) }} {{ $index === 1 ? 'whitespace-normal' : 'whitespace-nowrap' }}">
                        @if(is_array($cell) && isset($cell['name']))
                            {{ $cell['name'] }}
                        @else
                            {{ $cell }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach

        <!-- Category Types -->
        @foreach($category->types ?? [] as $type)
            <!-- Type Header -->
            <tr class="type-header-row">
                @foreach($type->header as $index => $header)
                    <td class="{{ $report->getAlignmentClass($index) }}">
                        {{ $header }}
                    </td>
                @endforeach
            </tr>

            <!-- Type Data -->
            @foreach($type->data as $typeRow)
                <tr class="type-data-row">
                    @foreach($typeRow as $index => $cell)
                        <td class="{{ $report->getAlignmentClass($index) }} {{ $index === 'account_name' ? 'whitespace-normal' : 'whitespace-nowrap' }}">
                            @if(is_array($cell) && isset($cell['name']))
                                {{ $cell['name'] }}
                            @else
                                {{ $cell }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach

            <!-- Type Summary -->
            <tr class="type-summary-row">
                @foreach($type->summary as $index => $cell)
                    <td class="{{ $report->getAlignmentClass($index) }}">
                        {{ $cell }}
                    </td>
                @endforeach
            </tr>
        @endforeach

        <tr class="category-summary-row">
            @foreach($category->summary as $index => $cell)
                <td
                    @class([
                        'cell',
                        $report->getAlignmentClass($index),
                        'underline-bold' => $loop->last,
                    ])
                >
                    {{ $cell }}
                </td>
            @endforeach
        </tr>

        <tr class="spacer-row">
            <td colspan="{{ count($report->getHeaders()) }}"></td>
        </tr>
        </tbody>
    @endforeach
    <tbody>
    <tr class="table-footer-row">
        @foreach ($report->getOverallTotals() as $index => $total)
            <td class="{{ $report->getAlignmentClass($index) }}">
                {{ $total }}
            </td>
        @endforeach
    </tr>
    </tbody>
</table>

<!-- Second Overview Table -->
<table class="table-class mt-4 border-t">
    <thead class="table-head">
    <tr>
        @foreach($report->getOverviewHeaders() as $index => $header)
            <th class="{{ $report->getAlignmentClass($index) }}">
                {{ $header }}
            </th>
        @endforeach
    </tr>
    </thead>
    <!-- Overview Content -->
    @foreach($report->getOverview() as $overviewCategory)
        <tbody>
        <tr class="category-header-row">
            @foreach($overviewCategory->header as $index => $header)
                <td class="{{ $report->getAlignmentClass($index) }}">
                    {{ $header }}
                </td>
            @endforeach
        </tr>
        @foreach($overviewCategory->data as $overviewAccount)
            <tr>
                @foreach($overviewAccount as $index => $cell)
                    <td class="{{ $report->getAlignmentClass($index) }} {{ $index === 'account_name' ? 'whitespace-normal' : 'whitespace-nowrap' }}">
                        @if(is_array($cell) && isset($cell['name']))
                            {{ $cell['name'] }}
                        @else
                            {{ $cell }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
        <!-- Summary Row -->
        <tr class="category-summary-row">
            @foreach($overviewCategory->summary as $index => $summaryCell)
                <td class="{{ $report->getAlignmentClass($index) }}">
                    {{ $summaryCell }}
                </td>
            @endforeach
        </tr>
       
        @if($overviewCategory->header['account_name'] === 'Starting Balance')
            @foreach($report->getOverviewAlignedWithColumns() as $summaryRow)
                <tr>
                    @foreach($summaryRow as $index => $summaryCell)
                        <td
                            @class([
                                'cell',
                                $report->getAlignmentClass($index),
                                'bold' => $loop->parent->last,
                                'underline-thin' => $loop->parent->remaining === 1 && $index === 'net_movement', // Thin underline
                                'underline-bold' => $loop->parent->last && $index === 'net_movement', // Bold underline
                            ])
                        >
                            {{ $summaryCell }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        @endif
        </tbody>
    @endforeach
</table>
</body>
</html>
