<x-filament-panels::page>
    {{ $this->form }}




    <div style="background-color: #fff; border-radius:8px;" class="p-6">
        To authenticate your request, generate and copy your API Key from the form above. Then include it with your <b>email</b> and <b>password</b> in the request header.
        <br><br>
        <b>Example:</b>
        <br>

        <div class="p-1">
        
            <pre>
                {
                    'email': 'your-email-address',
                    'password': 'your-password',
                    'api_key': 'your-API-Key',
                }
            </pre>

        </div>
    </div>


    <div>
        @assets

        <style>
            pre {
                background-color: #000000;
                color: #e0e0e0;
                padding: 10px;
                border-radius: 8px;
                overflow: auto;
                font-family: 'Courier New', Courier, monospace;
            }
            .keyword { color: #f22c3d; }
            .string { color: #7ec699; }
            .variable { color: #2e95d3; }
            .comment { color: #999999; }
            .constant { color: #e0c46c; }
        </style>

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
