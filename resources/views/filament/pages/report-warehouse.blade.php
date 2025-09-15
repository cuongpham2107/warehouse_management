<x-filament-panels::page>
    <style>
        .fi-ta-content-ctn {
            height: 800px;
        }
        .fi-ta-table thead .fi-ta-table-head-groups-row th {
            position: sticky;
            top: 0;
            z-index: 9999;
        }
        .fi-ta-table thead tr th {
            position: sticky;
            top: 32px;
            z-index: 9999;
        }

    </style>
    
    {{ $this->table }}
</x-filament-panels::page>
