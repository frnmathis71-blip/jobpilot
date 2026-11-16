@csrf

<div>
    <label for="PARENT_CHAMP">PARENT_CHAMP</label>
    <input type="text" id="PARENT_CHAMP" name="PARENT_CHAMP"
           value="{{ old('PARENT_CHAMP', $PARENT_SINGULIER->PARENT_CHAMP ?? '') }}">
    @error('PARENT_CHAMP')
        <p class="erreur">{{ $message }}</p>
    @enderror
</div>
