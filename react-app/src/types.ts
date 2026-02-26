export interface Currency {
  code: string;
  name: string;
  symbol: string;
}

export interface RatesResponse {
  base: string;
  rates: Record<string, number>;
  last_updated: string;
  source: 'live' | 'mock';
}

export interface ExchangeRatesHook {
  rates: Record<string, number> | null;
  loading: boolean;
  error: string | null;
  lastUpdated: string | null;
  source: string | null;
  refresh: () => void;
}
