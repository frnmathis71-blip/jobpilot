@csrf

<div>
    <label for="CHAMP1">CHAMP1</label>
    <input type="text" id="CHAMP1" name="CHAMP1"
           value="{{ old('CHAMP1', $SINGULIER->CHAMP1 ?? '') }}">
    @error('CHAMP1')
        <p class="erreur">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="CHAMP2">CHAMP2</label>
    <input type="number" step="0.01" id="CHAMP2" name="CHAMP2"
           value="{{ old('CHAMP2', $SINGULIER->CHAMP2 ?? '') }}">
    @error('CHAMP2')
        <p class="erreur">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="PARENT_SINGULIER_id">PARENT_SINGULIER</label>
    <select id="PARENT_SINGULIER_id" name="PARENT_SINGULIER_id">
        <option value="">-- Choisir --</option>
        @foreach ($PARENT_PLURIEL as $PARENT_SINGULIER)
            <option value="{{ $PARENT_SINGULIER->id }}"
                {{ old('PARENT_SINGULIER_id', $SINGULIER->PARENT_SINGULIER_id ?? '') == $PARENT_SINGULIER->id ? 'selected' : '' }}>
                {{ $PARENT_SINGULIER->PARENT_CHAMP }}
            </option>
        @endforeach
    </select>
    @error('PARENT_SINGULIER_id')
        <p class="erreur">{{ $message }}</p>
    @enderror
</div>
