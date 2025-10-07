{{-- Modal Component yang dapat digunakan ulang --}}
<div class="modal fade" id="{{ $id }}" 
     tabindex="-1" 
     aria-labelledby="{{ $id }}Label" 
     aria-hidden="true"
     @if(isset($backdrop)) data-bs-backdrop="{{ $backdrop }}" @endif
     @if(isset($keyboard)) data-bs-keyboard="{{ $keyboard ? 'true' : 'false' }}" @endif>
    <div class="modal-dialog {{ $size ?? 'modal-lg' }} {{ $scrollable ?? false ? 'modal-dialog-scrollable' : '' }} {{ $centered ?? false ? 'modal-dialog-centered' : '' }}">
        <div class="modal-content">
            @if(isset($title) || isset($headerButtons))
            <div class="modal-header {{ $headerClass ?? '' }}">
                @if(isset($title))
                <h5 class="modal-title" id="{{ $id }}Label">
                    @if(isset($icon))<i class="{{ $icon }}"></i> @endif
                    {{ $title }}
                </h5>
                @endif
                
                @if(isset($headerButtons))
                    {{ $headerButtons }}
                @endif
                
                @if(!isset($noCloseButton))
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                @endif
            </div>
            @endif

            <div class="modal-body {{ $bodyClass ?? '' }}">
                {{ $slot }}
            </div>

            @if(isset($footerButtons) || !isset($noFooter))
            <div class="modal-footer {{ $footerClass ?? '' }}">
                @if(isset($footerButtons))
                    {{ $footerButtons }}
                @else
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>