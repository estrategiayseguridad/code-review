@props(['for'])

@error($for)
<div class="form-text text-danger">{{ $message }}</div>
@enderror
