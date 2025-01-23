<?php

namespace Modules\Recruitment\Entities;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkSpace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'requirement',
        'terms_and_conditions',
        'branch',
        'category',
        'skill',
        'benefits',
        'position',
        'start_date',
        'end_date',
        'status',
        'applicant',
        'visibility',
        'code',
        'custom_question',
        'workspace',
        'qualify_lead',
        'receive_notification',
        'activete_pre_selection',
        'created_by',
        'id_assistant_openai_pre_selection',
        'id_assistant_openai_behavioral_test',
    ];

    protected static function newFactory()
    {
        return \Modules\Recruitment\Database\factories\JobFactory::new();
    }

    public static $status = [
        'active' => 'Ativo',
        'in_active' => 'Desativado',
    ];

    public static $job_type = [
        '' => 'Selecione o tipo',
        'full_time' => 'Tempo Integral',
        'part_time' => 'Autônomo',
    ];
    public function movies()
    {
        return $this->hasMany(JobMovies::class, 'job_id');
    }
    public function branches()
    {
        return $this->hasOne(\Modules\Hrm\Entities\Branch::class, 'id', 'branch');
    }

    public function categories()
    {
        return $this->hasOne(JobCategory::class, 'id', 'category');
    }
    public function customQuestions()
    {
        return $this->hasMany(JobCustomQuestion::class);
    }

    public function questions()
    {
        $ids = explode(',', $this->custom_question);

        return CustomQuestion::whereIn('id', $ids)->get();
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public static function defaultdata($company_id = null, $workspace_id = null)
    {
        $company_setting = [
            "job_template" => "template1",
            "job_color" => "2d2e31",
        ];

        $job_stages = [
            "Candidatos",
            "Pré-Seleção",
            "Teste Comportamental",
            "Aprovados",           
        ];

        if ($company_id == Null) {
            $companys = User::where('type', 'company')->get();
            foreach ($companys as $company) {
                $WorkSpaces = WorkSpace::where('created_by', $company->id)->get();
                foreach ($WorkSpaces as $WorkSpace) {
                    OfferLetter::defaultOfferLetter($company->id, $WorkSpace->id);

                    foreach ($job_stages as $job_stage) {
                        $jobstage = JobStage::where('title', $job_stage)->where('workspace', $WorkSpace->id)->where('created_by', $company->id)->first();
                        if ($jobstage == null) {
                            $jobstage = new JobStage();
                            $jobstage->title = $job_stage;
                            $jobstage->workspace =  !empty($WorkSpace->id) ? $WorkSpace->id : 0;
                            $jobstage->created_by = !empty($company->id) ? $company->id : 2;
                            $jobstage->save();
                        }
                    }

                    foreach ($company_setting as $key => $value) {
                        // Define the data to be updated or inserted
                        $data = [
                            'key' => $key,
                            'workspace' => !empty($WorkSpace->id) ? $WorkSpace->id : 0,
                            'created_by' => $company->id,
                        ];

                        // Check if the record exists, and update or insert accordingly
                        Setting::updateOrInsert($data, ['value' => $value]);
                    }
                }
            }
        } elseif ($workspace_id == Null) {
            $company = User::where('type', 'company')->where('id', $company_id)->first();
            $WorkSpaces = WorkSpace::where('created_by', $company->id)->get();
            foreach ($WorkSpaces as $WorkSpace) {
                foreach ($job_stages as $job_stage) {
                    OfferLetter::defaultOfferLetter($company->id, $WorkSpace->id);

                    $jobstage = JobStage::where('title', $job_stage)->where('workspace', $WorkSpace->id)->where('created_by', $company->id)->first();
                    if ($jobstage == null) {
                        $jobstage = new JobStage();
                        $jobstage->title = $job_stage;
                        $jobstage->workspace =  !empty($WorkSpace->id) ? $WorkSpace->id : 0;
                        $jobstage->created_by = !empty($company->id) ? $company->id : 2;
                        $jobstage->save();
                    }

                    foreach ($company_setting as $key => $value) {
                        // Define the data to be updated or inserted
                        $data = [
                            'key' => $key,
                            'workspace' => !empty($WorkSpace->id) ? $WorkSpace->id : 0,
                            'created_by' => $company->id,
                        ];
    
                        // Check if the record exists, and update or insert accordingly
                        Setting::updateOrInsert($data, ['value' => $value]);
                    }
                }
            }
        } else {
            $company = User::where('type', 'company')->where('id', $company_id)->first();
            $WorkSpace = WorkSpace::where('created_by', $company->id)->where('id', $workspace_id)->first();
            foreach ($job_stages as $job_stage) {
                OfferLetter::defaultOfferLetter($company->id, $WorkSpace->id);

                $jobstage = JobStage::where('title', $job_stage)->where('workspace', $WorkSpace->id)->where('created_by', $company->id)->first();
                if ($jobstage == null) {
                    $jobstage = new JobStage();
                    $jobstage->title = $job_stage;
                    $jobstage->workspace =  !empty($WorkSpace->id) ? $WorkSpace->id : 0;
                    $jobstage->created_by = !empty($company->id) ? $company->id : 2;
                    $jobstage->save();
                }

                foreach ($company_setting as $key => $value) {
                    // Define the data to be updated or inserted
                    $data = [
                        'key' => $key,
                        'workspace' => !empty($WorkSpace->id) ? $WorkSpace->id : 0,
                        'created_by' => $company->id,
                    ];
    
                    // Check if the record exists, and update or insert accordingly
                    Setting::updateOrInsert($data, ['value' => $value]);
                }
            }
        }
    }

    public static function GivePermissionToRoles($role_id = null, $rolename = null)
    {
        $staff_permission = [
            'career manage',
            'recruitment manage',
        ];

        $hr_permission = [
            'recruitment manage',
            'recruitment dashboard manage',
            'job manage',
            'job create',
            'job edit',
            'job delete',
            'job show',
            'jobcategory manage',
            'jobcategory create',
            'jobcategory edit',
            'jobcategory delete',
            'jobstage manage',
            'jobstage create',
            'jobstage edit',
            'jobstage delete',
            'jobapplication manage',
            'jobapplication create',
            'jobapplication show',
            'jobapplication delete',
            'jobapplication move',
            'jobapplication add skill',
            'jobapplication add note',
            'jobapplication delete note',
            'jobapplication archived manage',
            'jobapplication candidate manage',
            'jobapplication candidate create',
            'jobapplication candidate edit',
            'jobapplication candidate delete',
            'jobonboard manage',
            'jobonboard create',
            'jobonboard edit',
            'jobonboard delete',
            'jobonboard convert',
            'custom question manage',
            'custom question create',
            'custom question edit',
            'custom question delete',
            'interview schedule manage',
            'interview schedule create',
            'interview schedule edit',
            'interview schedule delete',
            'interview schedule show',
            'career manage',
            'letter offer manage',
            'job candidate manage',
            'job candidate create',
            'job candidate edit',
            'job candidate delete',
            'job experience manage',
            'job experience create',
            'job experience edit',
            'job experience show',
            'job experience delete',
            'job project manage',
            'job project create',
            'job project show',
            'job project edit',
            'job project delete',
            'experience candidate job manage',
            'experience candidate job create',
            'experience candidate job show',
            'experience candidate job edit',
            'experience candidate job delete',
            'job qualification manage',
            'job qualification create',
            'job qualification show',
            'job qualification edit',
            'job qualification delete',
            'job skill manage',
            'job skill create',
            'job skill show',
            'job skill edit',
            'job skill delete',
            'job award manage',
            'job award create',
            'job award show',
            'job award edit',
            'job award delete',
        ];

        if ($role_id == Null) {
            // staff
            $roles_v = Role::where('name', 'staff')->get();

            foreach ($roles_v as $role) {
                foreach ($staff_permission as $permission_v) {
                    $permission = Permission::where('name', $permission_v)->first();
                    if (!empty($permission)) {
                        if (!$role->hasPermission($permission_v)) {
                            $role->givePermission($permission);
                        }
                    }
                }
            }

            // hr
            $roles_v = Role::where('name', 'hr')->get();

            foreach ($roles_v as $role) {
                foreach ($hr_permission as $permission_v) {
                    $permission = Permission::where('name', $permission_v)->first();
                    if (!empty($permission)) {
                        if (!$role->hasPermission($permission_v)) {
                            $role->givePermission($permission);
                        }
                    }
                }
            }
        } else {
            if ($rolename == 'staff') {
                $roles_v = Role::where('name', 'staff')->where('id', $role_id)->first();
                foreach ($staff_permission as $permission_v) {
                    $permission = Permission::where('name', $permission_v)->first();
                    if (!empty($permission)) {
                        if (!$roles_v->hasPermission($permission_v)) {
                            $roles_v->givePermission($permission);
                        }
                    }
                }
            }

            if ($rolename == 'hr') {
                $roles_v = Role::where('name', 'hr')->where('id', $role_id)->first();
                foreach ($hr_permission as $permission_v) {
                    $permission = Permission::where('name', $permission_v)->first();
                    if (!empty($permission)) {
                        if (!$roles_v->hasPermission($permission_v)) {
                            $roles_v->givePermission($permission);
                        }
                    }
                }
            }
        }
    }
}
