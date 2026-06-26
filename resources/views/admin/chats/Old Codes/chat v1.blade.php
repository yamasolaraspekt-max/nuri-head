@extends('admin.layouts.app')
@section('title') CHAT @endsection

@section('style')
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .action-buttons { display: none; }
  .message-container:hover .action-buttons { display: flex; }
  .message-avatar { width: 32px; height: 32px; border-radius: 9999px; margin-right: 8px; }
  .emoji-list {
  position: absolute;
  bottom: 120%; /* 👈 opens above the button */
  left: 0;
  display: flex;
  flex-wrap: wrap;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 5px;
  max-width: 240px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  z-index: 50;
}
.emoji-list.hidden {
  display: none !important;
}
.emoji-list span {
  padding: 5px;
  cursor: pointer;
  font-size: 20px;
}

 
</style>
<style>
#chatBox .group:hover .group-hover\:flex {
    display: flex !important;
}

.hover-buttons  {

      top: 9px;
    right: 20px;
}
</style>
<style>
  #chatSidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    max-width: 300px;
    height: 100vh;
    z-index: 50;
    background: white;
  }

  #chatSidebar.visible {
    transform: translateX(0);
  }

  @media (min-width: 1024px) {
    #chatSidebar {
      transform: translateX(0) !important;
      position: static !important;
      width: 18rem;
      height: auto;
      z-index: auto;
    }
  }


  @media (min-width: 1024px) {
    #chatSidebar {
      transform: translateX(0%) !important;
      position: static !important;
    }
  }
</style>



@endsection

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper">
    <div class="content-body">
      <div class="flex flex-col lg:flex-row h-screen lg:h-[calc(100vh-100px)] mt-0 overflow-hidden">
        <!-- Sidebar (Users) -->
        <aside id="chatSidebar" class="w-full lg:w-72 bg-white border-r flex flex-col transition-all duration-300 translate-x-0 lg:translate-x-0 fixed lg:static z-40 h-screen lg:h-auto">
          <div class="p-4 flex items-center justify-between border-b">
            <div class="text-xl font-bold">Chats</div>
          </div>
          <button id="createGroupBtn" class="bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700 mb-3">
              ➕ Neue Gruppe erstellen
          </button>
 

          <div class="overflow-y-auto flex-1">
            <ul id="userList" class="divide-y p-2">
              <!-- Dynamic user list -->
            </ul>
          </div>
        </aside>

        <div id="groupModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
              <div class="bg-white p-6 rounded w-full max-w-md shadow">
                  <h2 class="text-lg font-bold mb-3">Neue Gruppe erstellen</h2>
                  <input id="groupName" type="text" placeholder="Gruppenname" class="w-full border rounded px-2 py-1 mb-3" />

                  <div id="userCheckboxList" class="max-h-40 overflow-y-auto border rounded p-2 mb-4">
                      <!-- Populated dynamically -->
                  </div>

                  <div class="flex justify-end gap-2">
                      <button id="cancelGroupBtn" class="px-3 py-1 border rounded">Abbrechen</button>
                      <button id="submitGroupBtn" class="px-3 py-1 bg-blue-600 text-white rounded">Erstellen</button>
                  </div>
              </div>
          </div>

        <!-- Chat Content -->
        <main class="flex-1 flex flex-col">
          <!-- Chat Header -->
          <div class="p-4 bg-white border-b flex items-center gap-3 shadow-sm">
            <h2 class="text-lg font-semibold" id="chatTitle">Wähle einen Kontakt</h2>
              <button onclick="toggleSidebar()"
                    class="lg:hidden absolute right-4 text-sm text-blue-600 z-50 top-4 bg-white px-2 py-1 rounded shadow">
              <i class="feather icon-users"></i> Kontakte
            </button>


            <span id="typingIndicator" class="text-sm text-gray-400 ml-auto hidden"><i class="fa fa-spinner"></i>schreibt...</span>
          </div>

          <!-- Chat Box -->
          <div id="chatBox" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-gray-100 to-white max-h-[50vh] lg:max-h-full">
            <!-- Messages will be appended here -->
          </div>

          <!-- Reply Box (Optional) -->
          <div id="replyBox" class="px-4 py-2 bg-gray-50 text-sm text-gray-600 hidden"></div>

          <!-- Message Input -->
          <div class="p-4 bg-white border-t flex items-center gap-3 relative">
            <div class="relative inline-block">
              <button id="emojiBtn" class="text-2xl">😊</button>
              <div id="emojiList" class="emoji-list hidden">
                <span>😀</span><span>😁</span><span>😂</span><span>🤣</span><span>😎</span><span>😍</span>
                <span>😢</span><span>😭</span><span>😡</span><span>👍</span><span>👎</span><span>🙏</span>
              </div>
            </div>


            <input id="messageInput" type="text" placeholder="Nachricht eingeben..."
              class="flex-1 border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button id="sendButton"
              class="bg-primary hover:bg-blue-700 text-white px-5 py-2 rounded-full shadow">Senden</button>
          </div>
        </main>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div id="toast"
  class="fixed bottom-4 right-4 bg-black text-white px-4 py-2 rounded shadow-lg opacity-0 transition-opacity duration-300 z-50">
</div>

<div id="replyBox" class="hidden mb-2 bg-gray-100 p-2 rounded text-sm text-gray-600 border-l-4 border-blue-500">
    <span id="replyText"></span>
    <button onclick="cancelReply()" class="float-right text-red-500 text-xs">✖</button>
</div>


<!-- Sound -->
<audio id="notificationSound" src="{{ asset('notification/notification.mp3') }}" preload="auto"></audio>
@endsection

@section('script')
@vite(['resources/js/chat.js'])
<script src="https://unpkg.com/feather-icons"></script>

<script>
  window.userId = {{ auth()->id() }};
  window.csrfToken = "{{ csrf_token() }}";
  window.notificationSound = "{{ asset('notification/notification.mp3') }}";
</script>
 
<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('chatSidebar');
    sidebar.classList.toggle('visible');
  }

</script>


@endsection
