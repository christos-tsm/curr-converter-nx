import type { RatesResponse } from '../types';
import mockRatesData from '../data/mockRates.json';

function getConfig() {
  return window.ccNxcodeData || { restUrl: '', nonce: '', useMock: true };
}

function getMockRates(base: string): RatesResponse {
  const usdRates = mockRatesData.rates as Record<string, number>;

  if (base === 'USD') {
    return {
      base: 'USD',
      rates: usdRates,
      last_updated: new Date().toISOString(),
      source: 'mock',
    };
  }

  const baseInUsd = usdRates[base] || 1;
  const converted: Record<string, number> = {};
  for (const [code, rate] of Object.entries(usdRates)) {
    converted[code] = parseFloat((rate / baseInUsd).toFixed(6));
  }

  return {
    base,
    rates: converted,
    last_updated: new Date().toISOString(),
    source: 'mock',
  };
}

export async function fetchRates(base: string = 'USD'): Promise<RatesResponse> {
  const config = getConfig();

  if (config.useMock || !config.restUrl) {
    return getMockRates(base);
  }

  const url = `${config.restUrl}rates?base=${encodeURIComponent(base)}`;

  const response = await fetch(url, {
    headers: {
      'X-WP-Nonce': config.nonce,
    },
  });

  if (!response.ok) {
    throw new Error(`HTTP ${response.status}`);
  }

  return response.json();
}
