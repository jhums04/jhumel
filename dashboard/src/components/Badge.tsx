import type { ReactNode } from 'react'

type BadgeVariant = 'solid' | 'outline' | 'muted' | 'dashed'

const variantClassNames: Record<BadgeVariant, string> = {
  solid: 'border-blue bg-blue text-white',
  outline: 'border-black text-black',
  muted: 'border-gray-200 text-gray-600',
  dashed: 'border-dashed border-gray-600 text-gray-600',
}

export function Badge({ variant, children }: { variant: BadgeVariant; children: ReactNode }) {
  return (
    <span className={`inline-flex rounded border px-2 py-0.5 text-xs font-semibold ${variantClassNames[variant]}`}>
      {children}
    </span>
  )
}
