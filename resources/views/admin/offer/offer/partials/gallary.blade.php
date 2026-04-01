@if($uploads->isEmpty())
  <div class="col-12 text-center text-muted">
    <i class="feather icon-folder" style="font-size: 48px;"></i>
    <p>Keine Dateien gefunden für dieses Angebot.</p>
  </div>
@else
  @foreach($uploads as $file)
    <div class="col-md-3 text-center mb-4" id="file-{{ $file->id }}">
      @if(Str::startsWith($file->file_type, 'image/'))
        <a href="{{ asset('uploads/' . $file->image) }}" class="glightbox" data-gallery="files">
          <img src="{{ asset('uploads/' . $file->image) }}" class="img-fluid rounded shadow" style="height: 150px; object-fit: cover;">
        </a>
      @else
        <div class="border p-3 bg-light">
          <i class="feather icon-file-text mb-2" style="font-size: 32px;"></i>
          <p class="small">{{ $file->image_name }}</p>
          <a href="{{ asset('uploads/' . $file->image) }}" target="_blank" class="btn btn-sm btn-outline-primary">Öffnen</a>
        </div>
      @endif

      <input type="text" class="form-control mt-2 mb-1" value="{{ $file->image_name }}"
             onblur="renameFile({{ $file->id }}, this.value)">
      <button class="btn btn-danger btn-sm" onclick="deleteFile({{ $file->id }})">Löschen</button>
    </div>
  @endforeach
@endif
