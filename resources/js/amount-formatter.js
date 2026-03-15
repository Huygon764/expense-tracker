/**
 * Format number with thousand separators (e.g. 1000000 → "1,000,000").
 * @param {number|string} value
 * @returns {string}
 */
export function formatNumber(value) {
  if (value === '' || value === null || value === undefined) return '';
  const num = typeof value === 'string' ? unformatNumber(value) : Number(value);
  if (Number.isNaN(num)) return '';
  const intPart = Math.floor(Math.abs(num)).toString();
  const formatted = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  return num < 0 ? '-' + formatted : formatted;
}

/**
 * Parse string with commas to number (e.g. "1,000,000" → 1000000).
 * @param {string|number} value
 * @returns {number}
 */
export function unformatNumber(value) {
  if (value === null || value === undefined) return 0;
  const str = typeof value === 'number' ? String(value) : String(value);
  const cleaned = str.replace(/[^\d.-]/g, '');
  if (cleaned === '' || cleaned === '-') return 0;
  const num = parseFloat(cleaned.replace(/,/g, ''));
  return Number.isNaN(num) ? 0 : num;
}

/**
 * Attach realtime formatting to an input; keeps cursor position reasonable.
 * @param {HTMLInputElement} input - The visible (display) input element
 * @param {function(string): void} onValueChange - Callback with raw number when value changes
 */
export function attachAmountFormatListener(input, onValueChange) {
  if (!input) return;

  function getDigitCountBeforePosition(str, pos) {
    return (str.slice(0, pos).match(/\d/g) || []).length;
  }

  function setCursorToDigitIndex(inputEl, digitIndex) {
    const str = inputEl.value;
    let count = 0;
    for (let i = 0; i <= str.length; i++) {
      if (count === digitIndex) {
        inputEl.setSelectionRange(i, i);
        return;
      }
      if (/\d/.test(str[i])) count++;
    }
    inputEl.setSelectionRange(str.length, str.length);
  }

  input.addEventListener('input', function () {
    const start = this.selectionStart ?? this.value.length;
    const digitCountBefore = getDigitCountBeforePosition(this.value, start);
    const raw = unformatNumber(this.value);
    const formatted = formatNumber(raw);
    this.value = formatted;
    setCursorToDigitIndex(this, digitCountBefore);
    if (onValueChange) onValueChange(raw);
  });
}

if (typeof window !== 'undefined') {
  window.amountFormatter = { formatNumber, unformatNumber, attachAmountFormatListener };
}
