import type { ReactNode } from 'react'

type DrawerCardProps = {
  title: string
  // Optional element beside the title, such as a "Drafted by AI" tag.
  aside?: ReactNode
  children: ReactNode
}

export function DrawerCard({ title, aside, children }: DrawerCardProps) {
  return (
    <section className="rounded-2xl border border-gray-200 bg-white p-5">
      <div className="mb-4 flex items-center justify-between gap-3">
        <h3 className="font-serif text-lg font-semibold">{title}</h3>
        {aside}
      </div>
      {children}
    </section>
  )
}
