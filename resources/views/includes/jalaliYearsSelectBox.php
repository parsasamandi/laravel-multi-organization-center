<!-- HTML select box for Jalali years -->
<label for="jalaliYear">سال:</label>
<select name="jalaliYear" id="jalaliYear" required>

<option value="1">
    <option value="2">1</option>
    <option value="3">2</option>
    <option value="4">3</option>
</select>

<!-- JavaScript to populate the select box -->
<script>
    // Function to populate the select box with Jalali years
    function populateJalaliYears() {
        // Get the select box element for Jalali years
        const selectBox = document.getElementById('jalaliYear');
        // Clear existing options
        selectBox.innerHTML = '';
        // Get the current Jalali year
        const currentYear = new Date().getFullYear() - 612; // Convert Gregorian to Jalali
        // Set the range of years
        const startYear = currentYear - 10;
        const endYear = currentYear + 10;
        // Loop through the range of years and create an option element for each year
        for (let year = startYear; year <= endYear; year++) {
            // Create an option element
            const option = document.createElement('option');
            // Set the value and text of the option
            option.value = year;
            option.textContent = year;
            // Append the option to the select box
            selectBox.appendChild(option);
        }
    }

    // Call the function to populate the select box with Jalali years
    populateJalaliYears();
</script>