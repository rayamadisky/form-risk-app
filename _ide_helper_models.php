<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string|null $kode_cabang
 * @property string $nama_cabang
 * @property string|null $nickname_cabang
 * @property int $is_active
 * @property int|null $korwil_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $korwil
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch active()
 * @method static \Database\Factories\BranchFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereKodeCabang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereKorwilId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereNamaCabang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereNicknameCabang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereUpdatedAt($value)
 */
	class Branch extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $risk_item_id
 * @property string $penyebab
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\RiskItem|null $item
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RiskMitigation> $mitigations
 * @property-read int|null $mitigations_count
 * @method static \Database\Factories\RiskCauseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskCause newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskCause newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskCause query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskCause whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskCause whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskCause wherePenyebab($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskCause whereRiskItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskCause whereUpdatedAt($value)
 */
	class RiskCause extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_risiko
 * @property string $kategori
 * @property string $role_target
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RiskCause> $causes
 * @property-read int|null $causes_count
 * @method static \Database\Factories\RiskItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskItem whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskItem whereNamaRisiko($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskItem whereRoleTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskItem whereUpdatedAt($value)
 */
	class RiskItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $risk_cause_id
 * @property string $mitigasi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\RiskCause|null $cause
 * @method static \Database\Factories\RiskMitigationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskMitigation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskMitigation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskMitigation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskMitigation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskMitigation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskMitigation whereMitigasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskMitigation whereRiskCauseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskMitigation whereUpdatedAt($value)
 */
	class RiskMitigation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $branch_id
 * @property string $tanggal_kejadian
 * @property string $tanggal_diketahui
 * @property int $risk_item_id
 * @property string|null $other_item_description
 * @property int|null $risk_cause_id
 * @property string|null $other_cause_description
 * @property string|null $mitigasi_tambahan
 * @property numeric|null $dampak_finansial
 * @property string|null $dampak_non_finansial
 * @property string|null $skala_dampak
 * @property string $kategori
 * @property string $approval_status
 * @property string $resolution_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\RiskCause|null $cause
 * @property-read \App\Models\RiskItem $item
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RiskReportLog> $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereApprovalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereDampakFinansial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereDampakNonFinansial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereMitigasiTambahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereOtherCauseDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereOtherItemDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereResolutionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereRiskCauseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereRiskItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereSkalaDampak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereTanggalDiketahui($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereTanggalKejadian($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReport whereUserId($value)
 */
	class RiskReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $risk_report_id
 * @property int $user_id
 * @property string $note
 * @property string $status_after_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReportLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReportLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReportLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReportLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReportLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReportLog whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReportLog whereRiskReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReportLog whereStatusAfterNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReportLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiskReportLog whereUserId($value)
 */
	class RiskReportLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property int $is_active
 * @property int $branch_id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Branch> $supervisedBranches
 * @property-read int|null $supervised_branches_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

