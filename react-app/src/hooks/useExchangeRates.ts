import { useState, useEffect, useCallback, useRef } from 'react';
import { fetchRates } from '../services/api';
import type { RatesResponse, ExchangeRatesHook } from '../types';

export default function useExchangeRates(baseCurrency: string): ExchangeRatesHook {
  const [rates, setRates] = useState<Record<string, number> | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [lastUpdated, setLastUpdated] = useState<string | null>(null);
  const [source, setSource] = useState<string | null>(null);
  const cache = useRef<Record<string, RatesResponse>>({});

  const loadRates = useCallback(async (base: string) => {
    if (cache.current[base]) {
      const cached = cache.current[base];
      setRates(cached.rates);
      setLastUpdated(cached.last_updated);
      setSource(cached.source);
      setLoading(false);
      setError(null);
      return;
    }

    setLoading(true);
    setError(null);

    try {
      const data = await fetchRates(base);
      cache.current[base] = data;
      setRates(data.rates);
      setLastUpdated(data.last_updated);
      setSource(data.source);
    } catch (err) {
      const message = err instanceof Error ? err.message : 'Failed to fetch rates';
      setError(message);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    if (baseCurrency) {
      loadRates(baseCurrency);
    }
  }, [baseCurrency, loadRates]);

  const refresh = useCallback(() => {
    delete cache.current[baseCurrency];
    loadRates(baseCurrency);
  }, [baseCurrency, loadRates]);

  return { rates, loading, error, lastUpdated, source, refresh };
}
