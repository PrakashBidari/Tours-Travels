<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'password',
        'role',
        'staff_role',
        'vendor_status',
        'company_name',
        'company_phone',
        'company_address',
        'company_description',
        'company_logo',
        'commission_rate',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
            'commission_rate' => 'decimal:2',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isRentalPartner(): bool
    {
        return $this->role === 'rental_partner';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /** Super admins and staff both use the /dashboard admin panel. */
    public function isAdminUser(): bool
    {
        return $this->isSuperAdmin() || $this->isStaff();
    }

    /** Whether this user may open an admin module (see config/travel.php `staff_roles`). */
    public function canAccessAdmin(string $module): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->isStaff()
            && in_array($module, config("travel.staff_roles.{$this->staff_role}.modules", []), true);
    }

    /** Super admins plus staff whose role covers `$module` — the recipients of admin alerts. */
    public static function adminsFor(string $module): \Illuminate\Database\Eloquent\Collection
    {
        return static::whereIn('role', ['super_admin', 'staff'])->get()
            ->filter(fn (self $user) => $user->canAccessAdmin($module))
            ->values();
    }

    public function getStaffRoleLabelAttribute(): ?string
    {
        return $this->staff_role ? config("travel.staff_roles.{$this->staff_role}.label") : null;
    }

    public function isApprovedVendor(): bool
    {
        return $this->isVendor() && $this->vendor_status === 'approved';
    }

    public function isApprovedRentalPartner(): bool
    {
        return $this->isRentalPartner() && $this->vendor_status === 'approved';
    }

    public function getCompanyLogoUrlAttribute(): ?string
    {
        return $this->company_logo ? Storage::disk('public')->url($this->company_logo) : null;
    }

    /** The dashboard URL this user should land on (used by the site nav and post-login redirect). */
    public function dashboardUrl(): string
    {
        return match (true) {
            $this->isAdminUser() => route('dashboard'),
            $this->isVendor() => route('vendor.dashboard'),
            $this->isRentalPartner() => route('rental-partner.dashboard'),
            default => route('account.dashboard'),
        };
    }

    /** Properties owned by this vendor. */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /** Destinations created by this vendor (or admin). */
    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    /** Bookings made by this user as a customer. */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Bookings received by this user as a vendor. */
    public function vendorBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'vendor_id');
    }

    /** Vehicle rental requests made by this user as a traveller. */
    public function vehicleRentalRequests(): HasMany
    {
        return $this->hasMany(VehicleRentalRequest::class);
    }

    /** Vehicle rental requests claimed by this user as a rental partner. */
    public function claimedRentalRequests(): HasMany
    {
        return $this->hasMany(VehicleRentalRequest::class, 'rental_partner_id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
