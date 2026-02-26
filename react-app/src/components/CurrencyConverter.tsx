import { useState, useCallback, useId, type ChangeEvent } from 'react';
import CurrencySelect from './CurrencySelect';
import SwapButton from './SwapButton';
import ConversionResult from './ConversionResult';
import useExchangeRates from '../hooks/useExchangeRates';

interface CurrencyConverterProps {
  defaultFrom?: string;
  defaultTo?: string;
}

export default function CurrencyConverter({ defaultFrom = 'USD', defaultTo = 'EUR' }: CurrencyConverterProps) {
  const uid = useId();
  const [fromCurrency, setFromCurrency] = useState(defaultFrom);
  const [toCurrency, setToCurrency] = useState(defaultTo);
  const [amount, setAmount] = useState('1');

  const { rates, loading, error, lastUpdated, source, refresh } =
    useExchangeRates(fromCurrency);

  const rate = rates ? rates[toCurrency] : null;

  const handleSwap = useCallback(() => {
    setFromCurrency(toCurrency);
    setToCurrency(fromCurrency);
  }, [fromCurrency, toCurrency]);

  const handleAmountChange = (e: ChangeEvent<HTMLInputElement>) => {
    const val = e.target.value;
    if (val === '' || /^\d*\.?\d*$/.test(val)) {
      setAmount(val);
    }
  };

  return (
    <div className="ccnx-converter">
      <div className="ccnx-header">
        <svg className="ccnx-header-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <circle cx="12" cy="12" r="10" />
          <path d="M12 6v12M6 12h12" />
        </svg>
        <span>Currency Converter</span>
      </div>

      <div className="ccnx-body">
        <div className="ccnx-field">
          <label className="ccnx-label" htmlFor={`${uid}-amount`}>
            Amount
          </label>
          <input
            id={`${uid}-amount`}
            className="ccnx-input"
            type="text"
            inputMode="decimal"
            value={amount}
            onChange={handleAmountChange}
            placeholder="0.00"
            autoComplete="off"
          />
        </div>

        <div className="ccnx-currencies">
          <CurrencySelect
            id={`${uid}-from`}
            label="From"
            value={fromCurrency}
            onChange={setFromCurrency}
          />

          <SwapButton onClick={handleSwap} />

          <CurrencySelect
            id={`${uid}-to`}
            label="To"
            value={toCurrency}
            onChange={setToCurrency}
          />
        </div>

        <ConversionResult
          amount={amount}
          fromCurrency={fromCurrency}
          toCurrency={toCurrency}
          rate={rate}
          loading={loading}
          error={error}
        />

        <div className="ccnx-footer">
          {lastUpdated && (
            <span className="ccnx-updated">
              {source === 'mock' ? 'Mock data' : `Updated: ${new Date(lastUpdated).toLocaleString()}`}
            </span>
          )}
          <button
            type="button"
            className="ccnx-refresh-btn"
            onClick={refresh}
            disabled={loading}
            title="Refresh rates"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
              <path d="M21 2v6h-6" />
              <path d="M3 12a9 9 0 0 1 15-6.7L21 8" />
              <path d="M3 22v-6h6" />
              <path d="M21 12a9 9 0 0 1-15 6.7L3 16" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  );
}
