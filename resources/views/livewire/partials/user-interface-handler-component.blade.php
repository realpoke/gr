<div>
    @script
        <script>
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
        </script>
    @endscript
</div>
