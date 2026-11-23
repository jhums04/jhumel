import { Route, Routes } from 'react-router'
import { ConnectionCheckPage } from './features/connection/ConnectionCheckPage'

export function App() {
  return (
    <Routes>
      <Route path="/" element={<ConnectionCheckPage />} />
    </Routes>
  )
}
