<div class="field">
    <label for="name">Name</label>
    <input id="name" name="name" value="{{ old('name', optional($customer)->name) }}" required maxlength="255">
</div>

<div class="field">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email', optional($customer)->email) }}" required maxlength="255">
</div>
