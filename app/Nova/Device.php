<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use NovaButton\Button;
use Saumini\Count\RelationshipCount;

class Device extends Resource
{
    public static $model = 'App\Models\Device';

    public static $title = 'id';

    public static $search = [
        'id','phone'
    ];

    public static $with = 'nearbies';


    public function fields(Request $request)
    {
        return [
            Text::make(__('Device MAC'), 'id')
                ->required()
                ->sortable(),
            Text::make(__('Nama Perangkat'), 'device_name'),
            Text::make('Label')
                ->help('optional')
                ->sortable(),
            Text::make(__('Telepon'), 'phone')
                ->help('optional')
                ->sortable(),
            DateTime::make(__('Terakhir Online'), 'updated_at')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->format("D-MM-Y hh:mm:ss")
                ->sortable(),
            Select::make(__('Health Condition'), 'health_condition')
                ->options(['healthy' => 'Sehat', 'pdp' => 'PDP', 'odp' => 'ODP', 'confirmed' => 'Positif'])
                ->displayUsingLabels()
                ->required()
                ->sortable(),
            Text::make('Area', 'last_known_area'),
            RelationshipCount::make('Riwayat Interaksi', 'nearbies')
                ->sortable(),
            HasMany::make('Riwayat Interaksi', 'nearbies', Nearby::class),
            Button::make('Lihat Aktifitas')->link(route('tracking.view',  [
                'device_id' => $this['id']
            ]))->style('primary')
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if ($area = $request->user()['area']) {
            $query = $query->where('last_known_area', 'like', "%$area%");
        }

        return $query->withCount('nearbies as nearbies');
    }
}
