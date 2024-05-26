<x-filament-panels::page>
    {{ $this->form }}




    <div style="background-color: #fff; border-radius:8px;" class="p-6">
        To authenticate your request, generate and copy your API Key from the form above. Then include it with your <b>email</b> and <b>password</b> in the request header.
        <br><br>
        <b>Example (To check balance):</b>
        <br>

        <div class="p-1">
        
            <pre>
                <span class="comment">// Initialize cURL session</span><br>
                <span class="variable">$ch</span> = <span class="keyword">curl_init</span>();<br><br>
        
                <span class="comment">// Set the URL</span><br>
                <span class="variable">$url</span> = <span class="string">'https://sweetbill.ng/api/v1/balance'</span>;<br><br>
        
                <span class="comment">// Set the headers</span><br>
                <span class="variable">$headers</span> = [<br>
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="string">'email: {your-email-address}'</span>,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="string">'password: {your-password}'</span>,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="string">'api_key: ABCDEFGHIJKLMN35cfd23ded9AG2OEBMN6635cfd23dedaSWEETBILL6635cfd23dedb562d0d6fWI32Me4Nfdee00cc232ed3GLadEdiEd3d2ud24d623geN6rf76L65Ei3T6fEdfdB3cd6S2E66ccb003dLd4WaAE6TccdBGI935c53OBMde32ed256IfA5B3SOLH5nd202405040704'</span><br>
                ];<br><br>
        
                <span class="comment">// Set cURL options</span><br>
                <span class="keyword">curl_setopt</span>(<span class="variable">$ch</span>, <span class="constant">CURLOPT_URL</span>, <span class="variable">$url</span>);<br>
                <span class="keyword">curl_setopt</span>(<span class="variable">$ch</span>, <span class="constant">CURLOPT_RETURNTRANSFER</span>, <span class="constant">true</span>);<br>
                <span class="keyword">curl_setopt</span>(<span class="variable">$ch</span>, <span class="constant">CURLOPT_HTTPHEADER</span>, <span class="variable">$headers</span>);<br><br>
        
                <span class="comment">// Execute the cURL request</span><br>
                <span class="variable">$response</span> = <span class="keyword">curl_exec</span>(<span class="variable">$ch</span>);<br><br>
        
                <span class="comment">// Check for cURL errors</span><br>
                <span class="keyword">if</span> (<span class="keyword">curl_errno</span>(<span class="variable">$ch</span>)) {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="keyword">echo</span> <span class="string">'Error:'</span> . <span class="keyword">curl_error</span>(<span class="variable">$ch</span>);<br>
                } <span class="keyword">else</span> {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">// Output the response</span><br>
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="keyword">echo</span> <span class="variable">$response</span>;<br>
                }<br><br>
        
                <span class="comment">// Close the cURL session</span><br>
                <span class="keyword">curl_close</span>(<span class="variable">$ch</span>);<br>
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
