<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;


    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
                
            if(auth()->user()->is_staff === true){
                return true;
            }else{
                return false;
            }
        }
 
        return true;
    }



    public function bulkmessage(): HasMany
    {
        return $this -> hasMany(BulkMessage::class);
    }

    public function transaction(): HasMany{
        return $this->hasMany(Transaction::class);
    }
    public function announcement() : HasOne{
        return $this -> hasOne(Announcement::class);
    }

    public function expense(): HasMany
    {
        return $this -> hasMany(Expense::class);
    }

    public function savings(): HasMany
    {
        return $this -> hasMany(Saving::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone_number',
        'position',
        'username',
        'add_user',
        'edit_user',
        'view_user',
        'delete_user',
        'add_expenses',
        'edit_expenses',
        'view_expenses',
        'delete_expenses',
        'add_savings',
        'edit_savings',
        'view_savings',
        'delete_savings',
        'add_customer',
        'edit_customer',
        'view_customer',
        'delete_customer',
        'can_announcement',
        'can_private_message',
        'can_view_transactions',
        'can_manage_services',
        'can_upgrade_customer',
        'can_reset_password',
        'can_credit_customer',
        'can_set_price',
        'widget_balance',
        'widget_user_balance',
        'widget_savings',
        'widget_expenses',
        'api_key',
        'widget_refund',
        'widget_cashflow',
        'widget_sales',
        'toggle_payment_method',
        'set_charges',
        'set_cashback',
        'set_referral',
        'user_status',
        'profile_image',
    'balance',
    'referral_code',
    'package',
    'transaction_pin',
    'bvn',
    'nin',
    'bvn_date_of_birth',
    'cashback_balance',
    'referral_balance',
    'monniepoint_acct',
    'wema_acct',
    'sterling_acct',
    'gtb_acct',
    'providus_acct',
    'rehoboth_acct',
    'fidelity_acct',
    'paystack_acct',
    'flutterwave_acct',
    'is_staff',
    'has_accounts',
    'accounts',
    'account_reference',
    'filled_kyc',
    'balance_toggle',
    
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
       
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'add_user' => 'boolean',
        'balance_toggle' => 'boolean',
        'filled_kyc' => 'boolean',
        'edit_user' => 'boolean',
        'view_user' => 'boolean',
        'delete_user' => 'boolean',
        'add_expenses' => 'boolean',
        'edit_expenses' => 'boolean',
        'has_accounts' => 'boolean',
        'view_expenses' => 'boolean',
        'delete_expenses' => 'boolean',
        'add_savings' => 'boolean',
        'edit_savings' => 'boolean',
        'view_savings' => 'boolean',
        'delete_savings' => 'boolean',
        'add_customer' => 'boolean',
        'edit_customer' => 'boolean',
        'view_customer' => 'boolean',
        'delete_customer' => 'boolean',
        'can_announcement' => 'boolean',
        'can_private_message' => 'boolean',
        'can_view_transactions' => 'boolean',
        'can_manage_services' => 'boolean',
        'can_upgrade_customer' => 'boolean',
        'can_reset_password' => 'boolean',
        'can_credit_customer' => 'boolean',
        'can_set_price' => 'boolean',
        'widget_balance' => 'boolean',
        'widget_user_balance' => 'boolean',
        'widget_savings' => 'boolean',
        'widget_expenses' => 'boolean',
        'widget_refund' => 'boolean',
        'widget_cashflow' => 'boolean',
        'widget_sales' => 'boolean',
        'toggle_payment_method' => 'boolean',
        'set_charges' => 'boolean',
        'set_cashback' => 'boolean',
        'set_referral' => 'boolean',
        'user_status' => 'boolean',
        'profile_image' => 'array',
        'accounts' => 'array',
        'is_staff' => 'boolean'
        ];
    }
}
