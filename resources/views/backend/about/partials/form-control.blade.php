<div class="position-relative form-group">
    <label for="judul" class="">Judul</label>
    <input name="judul" id="judul" placeholder="judul" type="text"
        class="form-control"
        value="{{old('judul', $item->judul)}}">
    @error('judul')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
<div class="position-relative form-group">
    <label for="Text Top" class="">Text Top</label>
    <textarea name="text_top" class="form-control ck-editor @error('text_top') is-invalid @enderror">{{ old('text_top', $item->text_top) }}</textarea>
    @error('text_top')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="position-relative form-group">
    <label for="konten" class="">Konten</label>
    <textarea name="konten" class="form-control ck-editor2 @error('konten') is-invalid @enderror">{{ old('konten', $item->konten) }}</textarea>
    @error('konten')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>