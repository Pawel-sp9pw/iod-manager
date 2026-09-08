@php($c=$company??null)
<div class="iod-field"><label class="iod-label">Nazwa *</label><input name="name" required value="{{ old('name',$c?->name) }}" class="iod-input"></div>
<div class="iod-field"><label class="iod-label">Nazwa skrócona</label><input name="short_name" value="{{ old('short_name',$c?->short_name) }}" class="iod-input"></div>
<div class="iod-field"><label class="iod-label">NIP</label><input name="nip" value="{{ old('nip',$c?->nip) }}" class="iod-input"></div>
<div class="iod-field"><label class="iod-label">REGON</label><input name="regon" value="{{ old('regon',$c?->regon) }}" class="iod-input"></div>
<div class="iod-field"><label class="iod-label">KRS</label><input name="krs" value="{{ old('krs',$c?->krs) }}" class="iod-input"></div>
<div class="iod-field"><label class="iod-label">E-mail</label><input type="email" name="email" value="{{ old('email',$c?->email) }}" class="iod-input"></div>
<div class="iod-field"><label class="iod-label">Telefon</label><input name="phone" value="{{ old('phone',$c?->phone) }}" class="iod-input"></div>
<div class="iod-field"><label class="iod-label">Adres</label><input name="address" value="{{ old('address',$c?->address) }}" class="iod-input"></div>
<div class="iod-field"><label class="iod-label">Kod pocztowy</label><input name="postal_code" value="{{ old('postal_code',$c?->postal_code) }}" class="iod-input"></div>
<div class="iod-field"><label class="iod-label">Miasto</label><input name="city" value="{{ old('city',$c?->city) }}" class="iod-input"></div>
<div class="iod-field" style="grid-column:1/-1"><label class="iod-label">Notatki</label><textarea name="notes" rows="4" class="iod-textarea">{{ old('notes',$c?->notes) }}</textarea></div>
<label style="grid-column:1/-1;display:flex;align-items:center;gap:9px;font-size:14px;color:#475569"><input type="checkbox" name="active" value="1" @checked(old('active',$c?->active??true)) style="width:16px;height:16px"> Aktywna firma</label>
