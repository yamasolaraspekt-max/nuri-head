<script src="/build/assets/app.js"></script> {{-- From Vite --}}
<script>
    window.Echo.channel('notifications')
        .listen('.new.message', (e) => {
            console.log('🎉 Reverb Message:', e.message);
            alert('New message: ' + e.message);
        });
</script>
