import CurrencyConverter from './components/CurrencyConverter';

interface AppProps {
  defaultFrom: string;
  defaultTo: string;
}

export default function App({ defaultFrom, defaultTo }: AppProps) {
  return <CurrencyConverter defaultFrom={defaultFrom} defaultTo={defaultTo} />;
}
