<x-filament::widget>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Acciones rápidas</h2>
    </x-slot>

    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-filament::button tag="a" 
            href="mantenimiento" 
            id="btnMantenimiento"
            class="w-full" style="font-size:2em;padding: 1em 1em;">
            MANTENIMIENTO
            </x-filament::button>

            <x-filament::button tag="a" 
            href="service" 
            id="btnService"
            class="w-full" style="font-size:2em;padding: 1em 1em;">
            SERVICE
            </x-filament::button>

            <x-filament::button tag="a" 
            href="roturasReparacion" 
            id="btnRoturasReparaciones"
            class="w-full" style="font-size:2em;padding: 1em 1em;">
            ROTURAS / REPARACIONES
            </x-filament::button>

            <x-filament::button tag="a" 
            href="combustiblesLubricantes" 
            id="btnCombustiblesLubricantes"
            class="w-full" style="font-size:2em;padding: 1em 1em;line-height: 1.2em;text-align:center">
            COMBUSTIBLES / LUBRICANTES
            </x-filament::button>
            
        </div>
    </div>
 

</x-filament::widget>