import currencies from '../data/currencies';

interface CurrencySelectProps {
  value: string;
  onChange: (value: string) => void;
  label: string;
  id: string;
}

export default function CurrencySelect({ value, onChange, label, id }: CurrencySelectProps) {
  return (
    <div className="ccnx-field">
      <label className="ccnx-label" htmlFor={id}>
        {label}
      </label>
      <select
        id={id}
        className="ccnx-select"
        value={value}
        onChange={(e) => onChange(e.target.value)}
      >
        {currencies.map((cur) => (
          <option key={cur.code} value={cur.code}>
            {cur.code} — {cur.name}
          </option>
        ))}
      </select>
    </div>
  );
}
