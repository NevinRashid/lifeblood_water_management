<?php

namespace Modules\UsersAndTeams\Services;

use App\Traits\HandleServiceErrors;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\UsersAndTeams\Models\Team;
use Modules\UsersAndTeams\Models\User;

class TeamService
{
    use HandleServiceErrors;
    /**
     * Get all teams from database
     *
     * @return array $arraydata
     */
    public function getAllTeams(array $filters = [], int $perPage = 10)
    {
        try{
            $query = Team::with(['members','reforms']);

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['name'])) {
                $query->where('name', $filters['name']);
            }

            $teams = Cache::remember('all_teams', 3600, function() use($query, $perPage){
                    $teams= $query->paginate($perPage);
                    return $teams;
                });
            return $teams;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Get a single team with its relationships.
     *
     * @param  Team $team
     *
     * @return Team $team
     */
    public function showTeam(Team $team)
    {
        try{
            return $team->load([
                    'members',
                    'reforms',
                ])->loadCount(['members','reforms']);

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Add new team to the database.
     *
     * @param array $arraydata
     *
     * @return Team $team
     */
    public function createTeam(array $data)
    {
        try{
            return DB::transaction(function () use ($data) {
                $team = Team::create($data);
                Cache::forget("all_teams");
                return $team;
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Update the specified team in the database.
     *
     * @param array $arraydata
     * @param Team $team
     *
     * @return Team $team
     */

    public function updateTeam(array $data, Team $team){
        try{
            $team->update(array_filter($data));
            Cache::forget("all_teams");
            return $team;

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Delete the specified team from the database.
     *
     * @param Team $team
     *
     */
    public function deleteTeam(Team $team){
        try{
            return DB::transaction(function () use ($team) {
                Cache::forget("all_teams");
                return $team->delete();
            });

        } catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }

    /**
     * Assign members for the team,
     *
     * @param array $memberIdsdata
     * @param Team $team
     *
     * @return Team $team
     */
    public function assignTeamMembers( array $data, Team $team){
        try{
            $memberIds = $data['member_ids'];
            $assigned =[];
            $alreadyInTeam =[];
            $skipped =[];
            foreach($memberIds as $userId)
            {
                $user= User::findOrfail($userId);
                if(!$user->team_id){
                    $user->update(['team_id'=>$team->id]);
                    $assigned[] = $userId;
                }
                elseif($user->team_id === $team->id){
                    $alreadyInTeam[] = $userId;
                }
                else{
                    $skipped[] = $userId;
                }
            }
            Cache::forget("all_teams");
            $result = [
                    'team with members'     =>$team->load('members')->loadCount('members'),
                    'assigned members'      =>$assigned,
                    'alreadyInTeam members' =>$alreadyInTeam,
                    'skipped members'       =>$skipped
                    ];
            return $result;

        }catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }
    /**
     * Remove members from the team,
     *
     * @param array $memberIdsdata
     * @param Team $team
     *
     * @return Team $team
     */
    public function removeTeamMembers( array $data, Team $team){
        try{
            $memberIds = $data['member_ids'];
            $removed  =[];
            $notInTeam  =[];
            foreach($memberIds as $userId)
            {
                $user= User::findOrfail($userId);
                if($user->team_id === $team->id){
                    $user->update(['team_id'=>null]);
                    $removed[] = $userId;
                }
                else{
                    $notInTeam[]= $userId;
                }
            }
            Cache::forget("all_teams");
            $result = [
                    'team with members' =>$team->load('members')->loadCount('members'),
                    'removed members'   =>$removed,
                    'notInTeam members' =>$notInTeam
                    ];
            return $result;

        }catch(\Throwable $th){
            return $this->error("An error occurred",500, $th->getMessage());
        }
    }
}
