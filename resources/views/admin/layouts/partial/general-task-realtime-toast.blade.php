{{-- Include this once in admin.layouts.app before </body> --}}
<div class="gt-toast-wrap" id="globalTaskToastWrap"></div>
<audio id="globalTaskSound" preload="auto">
  <source src="{{ asset('sounds/task-notification.mp3') }}" type="audio/mpeg">
</audio>
<script>
  window.addEventListener('general-task-toast', function(){
    const audio = document.getElementById('globalTaskSound');
    if (audio) audio.play().catch(function(){});
  });
  if (window.Echo) {
    window.Echo.private('general-tasks')
      .listen('.general-task.changed', function(e){
        if (window.gtToast) window.gtToast('info', e.title || 'Aufgabe aktualisiert', e.message || 'Eine Aufgabe wurde geändert.');
      });
  }
</script>
