import { getCurrencyByCode } from '../data/currencies';

interface ConversionResultProps {
  amount: string;
  fromCurrency: string;
  toCurrency: string;
  rate: number | null;
  loading: boolean;
  error: string | null;
}

export default function ConversionResult({
  amount,
  fromCurrency,
  toCurrency,
  rate,
  loading,
  error,
}: ConversionResultProps) {
  if (error) {
    return (
      <div className="ccnx-result ccnx-result--error">
        <span className="ccnx-result-icon">⚠</span>
        <span>{error}</span>
      </div>
    );
  }

  if (loading) {
    return (
      <div className="ccnx-result ccnx-result--loading">
        <div className="ccnx-spinner" />
        <span>Loading rates…</span>
      </div>
    );
  }

  const numericAmount = Number(amount);
  if (!rate || !amount || isNaN(numericAmount) || numericAmount <= 0) {
    return (
      <div className="ccnx-result ccnx-result--empty">
        Enter an amount to convert
      </div>
    );
  }

  const converted = (numericAmount * rate).toFixed(2);
  const fromInfo = getCurrencyByCode(fromCurrency);
  const toInfo = getCurrencyByCode(toCurrency);

  const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });

  return (
    <div className="ccnx-result ccnx-result--success">
      <div className="ccnx-result-from">
        {formatter.format(numericAmount)} {fromInfo.code}
      </div>
      <div className="ccnx-result-equals">=</div>
      <div className="ccnx-result-to">
        <span className="ccnx-result-value">{formatter.format(Number(converted))}</span>
        <span className="ccnx-result-currency">{toInfo.code}</span>
      </div>
      <div className="ccnx-result-rate">
        1 {fromInfo.code} = {rate.toFixed(6)} {toInfo.code}
      </div>
    </div>
  );
}
