<x-filament-panels::page>

<div>
    <form wire:submit="withdrawlCashback">
        {{ $this->form }}
        <div style="padding:19px 0px;">

        
            <button type="submit" 
            wire:loading.attr="disabled"
            
            >
            
            <span wire:loading.remove
                   style="background:#fe5006; padding:7px 11px; color:#fff; border-radius:3px;"
            >Withdraw</span>
           <span wire:loading
                   style="background:#ec5e21; cursor:no-drop; color:#fff; opacity:0.6; padding:7px 11px; border-radius:3px;"
           >
           processing...</span>
            </button>


    </div>
    </form>
    
    <x-filament-actions::modals />


    @assets

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @endassets

    @script
    <script>
        document.addEventListener('alert', (event) => {
            let alertData = event.detail;


            Swal.fire({
            title: alertData.title,
            text: alertData.text,
            icon: alertData.type,
            confirmButtonText: alertData.button
            })

        });
        
    </script>

    @endscript
</div>

</x-filament-panels::page>
