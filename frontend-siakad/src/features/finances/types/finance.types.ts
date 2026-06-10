export type FinanceType = 'spp' | 'tabungan'
export type PaymentMethodSchool = 'cash' | 'transfer'
export type SavingsTransactionType = 'deposit' | 'withdrawal'

export interface FinanceRecord {
  id: number
  student_id: number
  student_name: string | null
  student_nisn: string | null
  class_name: string | null
  type: FinanceType
  type_label: string
  amount: number
  amount_formatted: string
  description: string | null
  month: string
  is_paid: boolean
  payment_method: PaymentMethodSchool | null
  payment_method_label: string | null
  transaction_type: SavingsTransactionType | null
  transaction_type_label: string | null
  paid_at: string | null
  created_at: string
}

export interface FinanceSummary {
  spp_collected: number
  spp_pending: number
  savings_balance: number
  total_deposits: number
  total_withdrawals: number
  recent_transactions: FinanceRecord[]
}

export interface StudentFinanceSummary {
  student: {
    id: number
    name: string
    nisn: string | null
  }
  spp: {
    total_paid: number
    history: FinanceRecord[]
  }
  savings: {
    balance: number
    total_deposits: number
    total_withdrawals: number
    history: FinanceRecord[]
  }
}

export interface SppPaymentPayload {
  student_id: number
  amount: number
  month: string
  payment_method: PaymentMethodSchool
  description?: string
  is_paid?: boolean
}

export interface SavingsPayload {
  student_id: number
  amount: number
  transaction_type: SavingsTransactionType
  description?: string
}
