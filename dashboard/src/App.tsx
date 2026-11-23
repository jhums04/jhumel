import { Route, Routes } from 'react-router'
import { AppLayout } from './components/AppLayout'
import { LoginPage } from './features/auth/LoginPage'
import { RequireAuth } from './features/auth/RequireAuth'
import { ConnectionCheckPage } from './features/connection/ConnectionCheckPage'

export function App() {
  return (
    <Routes>
      <Route path="/login" element={<LoginPage />} />
      <Route element={<RequireAuth />}>
        <Route element={<AppLayout />}>
          <Route index element={<ConnectionCheckPage />} />
        </Route>
      </Route>
    </Routes>
  )
}
