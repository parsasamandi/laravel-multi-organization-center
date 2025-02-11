<?php
    use Morilog\Jalali\Jalalian;
    use App\Providers\Convertor;

    $convertor = new Convertor();

    // Get the current Jalali year
    $currentJalaliDate = Jalalian::now();
    $currentYear = $currentJalaliDate->getYear();

    // Define the range of years (e.g., from 1400 to the current year)
    $startYear = 1402;
?>

<!-- HTML select box for Jalali years -->
<label for="jalaliYear">سال: <span class="input-required">*</span></label>
<select name="jalaliYear" id="jalaliYear">
    <?php for ($year = $startYear; $year <= $currentYear; $year++): ?>
        <option value="<?= $year ?>"><?= $convertor->englishToPersianDecimal($year) ?></option>
    <?php endfor; ?>
</select>
