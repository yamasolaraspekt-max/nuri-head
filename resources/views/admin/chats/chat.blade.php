@extends('admin.layouts.app')
@section('title') CHATS @endsection
@section('style') 
<style>
.gradient-custom {
/* fallback for old browsers */
background: #fccb90;

/* Chrome 10-25, Safari 5.1-6 */
background: -webkit-linear-gradient(to bottom right, rgba(252, 203, 144, 1), rgba(213, 126, 235, 1));

/* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
background: linear-gradient(to bottom right, rgba(252, 203, 144, 1), rgb(144 196 76));)
}

.mask-custom {
background: rgba(24, 24, 16, .2);
border-radius: 2em;
backdrop-filter: blur(15px);
border: 2px solid rgba(255, 255, 255, 0.05);
background-clip: padding-box;
box-shadow: 10px 10px 10px rgba(46, 54, 68, 0.03);
}
</style>
@endsection

@section('content')
     <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">CHATS</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a></li>
                                    <li class="breadcrumb-item"><a href="#">BITRIX CHATS</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <section class="gradient-custom p-5">
                    <div class="row">
                        <!-- Chat List -->
                        <div class="col-md-3 col-lg-3 col-xl-3 mb-4 mb-md-0">
                            <h5 class="font-weight-bold mb-3 text-center text-white">Member</h5>
                            <div class="card mask-custom">
                                <div class="card-body">
                                    <div class="chat-container" style="max-height: 1000px; overflow-y: auto; padding-right: 10px;">
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($chats as $chat)
                                                <li class="p-2 border-bottom"
                                                    style="border-bottom: 1px solid rgba(255,255,255,.3) !important;"
                                                    data-message-id="{{ $chat->id }}" >
                                                    <a href="#!" class="d-flex justify-content-between link-light user-link">
                                                        <div class="d-flex flex-row">
                                                            <div class="pt-1">
                                                                <p class="fw-bold mb-0 black">{{ $chat->id }}.{{ $chat->name }}</p>
                                                                <p class="small text-white">
                                                                    {{ substr($chat->description, 0, 20) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="pt-1">
                                                            <p class="small text-white mb-1">
                                                                {{ \Carbon\Carbon::parse($chat->date_create)->diffForHumans() }}
                                                            </p>
                                                            <span class="badge bg-danger float-end">{{ $chat->message_count }}</span>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Section -->
                        <div class="col-md-8 col-lg-8 col-xl-8"> 
                           <div id="messages-container" style="max-height: 1000px; overflow-y: auto; padding-right: 10px;">
                                @foreach ($chats as $chat)
                                    <ul class="list-unstyled text-white chats-container"
                                        data-message-id="{{ $chat->id }}"
                                        style="display: none;">
                                        @foreach ($messages->where('chat_id', $chat->id) as $msg)
                                            <li class="d-flex justify-content-between mb-4">
                                                <img src="{{ $msg->user_avatar }}"
                                                    alt="avatar"
                                                    class="rounded-circle d-flex align-self-start me-3 shadow-1-strong"
                                                    width="60">
                                                <div class="card mask-custom">
                                                    <div class="card-header d-flex justify-content-between p-3"
                                                        style="border-bottom: 1px solid rgba(255,255,255,.3);">
                                                        <p class="fw-bold mb-0">{{ $msg->user_name }}</p>
                                                        <p class="text-light small mb-0">
                                                            <i class="far fa-clock"></i>{{ \Carbon\Carbon::parse($msg->date)->isoFormat('DD.MM.YYYY') }}
                                                        </p>
                                                    </div>
                                                    <div class="card-body">
                                                        <p class="mb-0">{{ $msg->text }}</p>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </section>
            </div>

        </div>
    </div>
    <!-- END: Content-->
@endsection

@push('scripts') 
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Select all chat list items
        const chatListItems = document.querySelectorAll('.list-unstyled.mb-0 li');

        chatListItems.forEach(chatItem => {
            chatItem.addEventListener('click', () => {
                // Get the message ID from the clicked chat item
                const messageId = chatItem.getAttribute('data-message-id');

                // Hide all message containers
                const allMessageContainers = document.querySelectorAll('.chats-container');
                allMessageContainers.forEach(container => {
                    container.style.display = 'none';
                });

                // Show only the message container that matches the clicked chat's message ID
                const matchingContainer = document.querySelector(`.chats-container[data-message-id="${messageId}"]`);
                if (matchingContainer) {
                    matchingContainer.style.display = 'block';
                }
            });
        });
    });
</script>

 
@endpush