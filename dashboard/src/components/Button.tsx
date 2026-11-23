import type { ComponentProps } from 'react'

type ButtonProps = Omit<ComponentProps<'button'>, 'type'> & {
  type?: 'button' | 'submit'
  variant?: 'primary' | 'secondary'
}

const variantClassNames = {
  primary: 'bg-blue text-white hover:bg-blue-dark',
  secondary: 'border border-black text-black hover:bg-black hover:text-white',
}

export function Button({ type = 'button', variant = 'primary', className = '', ...buttonProps }: ButtonProps) {
  return (
    <button
      type={type === 'submit' ? 'submit' : 'button'}
      className={`inline-flex items-center justify-center rounded px-5 py-3 font-semibold disabled:cursor-not-allowed disabled:opacity-60 ${variantClassNames[variant]} ${className}`}
      {...buttonProps}
    />
  )
}
