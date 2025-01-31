@use(Illuminate\Support\Facades\Route)

<div>
    @script
        <script defer>
            const handleSessionExpiration = ({ status, preventDefault }) => {
                if (status === 419) {
                    Flux.toast('Your session has expired. Please login again.');
                    Livewire.navigate('{{ route('authenticate.page') }}');
                    preventDefault();
                }
            };

            document.addEventListener('livewire:init', () => {
                Livewire.hook('request', ({ fail }) => {
                    fail(handleSessionExpiration);
                });
            });

            document.addEventListener('livewire:navigated', () => {
                Livewire.hook('request', ({ fail }) => {
                    fail(handleSessionExpiration);
                });
            });

            Echo.private('Interface.{{ $this->user->id }}')
                .listen('PrivateFoundClaimableComputerEvent', () => {
                    if ('setting.page' != '{{ Route::currentRouteName() }}') {
                        Flux.modal('claim-computer-modal').show();
                    }
                });
        </script>
    @endscript
</div>
