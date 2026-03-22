import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import { marked } from 'marked';
import { formatNumber, unformatNumber, attachAmountFormatListener } from './amount-formatter.js';

window.Alpine = Alpine;
Alpine.start();

window.Chart = Chart;
window.marked = marked;
window.amountFormatter = { formatNumber, unformatNumber, attachAmountFormatListener };
