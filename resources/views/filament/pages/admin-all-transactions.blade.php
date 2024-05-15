<x-filament-panels::page>
    {{ $this->table }}






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
</x-filament-panels::page>
