/// <reference types="vite/client" />

interface CcNxcodeData {
  restUrl: string;
  nonce: string;
  useMock: boolean;
}

declare global {
  interface Window {
    ccNxcodeData?: CcNxcodeData;
  }
}
