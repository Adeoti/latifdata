<div>
   
    @if ($announcement_status === true)

              @if ($announcement_style == 'scroll')

                    <marquee behavior="scroll" direction="left" onmouseover="this.stop()" onmouseout="this.start()" style="border: 1px dotted gray; border-radius: 4px; padding: 5px; ">
                    {!! $announcement_content !!}
                    </marquee>

              @endif




               @if ($announcement_style == 'banner')

                    <div style="background-color: rgba(255, 255, 255, 0.904); color:#fe5006; padding:5px; border-radius:4px; border-left:3px solid #fe5006;">
                        {!! $announcement_content !!}
                    </div>

               @endif



               @if ($announcement_style == 'pop')
                
                    <div id="popElem" rel="on" style="display: none;">
                        {!! $announcement_content !!}
                    </div>

               @endif

    @endif


            <div id="popElem" rel="of">
                
            </div>



    @assets

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @endassets

    @script
    <script>
        let popElem = document.getElementById('popElem');

        if(popElem.getAttribute('rel') == "on"){


            Swal.fire({
            title: 'Announcement',
            html: popElem.innerHTML,
            icon: 'info',
            confirmButtonText: 'Got it!'
            });


        }
       
        
    </script>

    @endscript
</div>
