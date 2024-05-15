<x-filament-panels::page>
    @livewire('database-notifications')
<div>
    
    <form wire:submit="buyAirtime">
            {{ $this->form }}
            <div style="padding:19px 0px;">
    
            
                <button type="submit" 
                wire:loading.attr="disabled"
                
                >
                
                <span wire:loading.remove
                style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
                class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action"
                > 
                
    
              
                <svg wire:loading.remove.delay.default="1" wire:target="purchase" class="fi-btn-icon transition duration-75 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                    <path fill-rule="evenodd" d="M6 5v1H4.667a1.75 1.75 0 0 0-1.743 1.598l-.826 9.5A1.75 1.75 0 0 0 3.84 19H16.16a1.75 1.75 0 0 0 1.743-1.902l-.826-9.5A1.75 1.75 0 0 0 15.333 6H14V5a4 4 0 0 0-8 0Zm4-2.5A2.5 2.5 0 0 0 7.5 5v1h5V5A2.5 2.5 0 0 0 10 2.5ZM7.5 10a2.5 2.5 0 0 0 5 0V8.75a.75.75 0 0 1 1.5 0V10a4 4 0 0 1-8 0V8.75a.75.75 0 0 1 1.5 0V10Z" clip-rule="evenodd"></path>
                  </svg> 
                Proceed</span>
               <span wire:loading
                       style="background:#ec5e21; color:#fff; font-weight:500; cursor:no-drop; opacity:0.6; padding:7px 11px; border-radius:3px;"
               >
    
    
               <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="animate-spin fi-btn-icon transition duration-75 h-5 w-5 text-white" wire:loading.delay.default="" wire:target="purchase" style="display: inline-block;">
                <path clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill-rule="evenodd" fill="currentColor" opacity="0.2"></path>
                <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" fill="currentColor"></path>
            </svg>
    
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
