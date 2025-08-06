<?php

namespace Modules\UsersAndTeams\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Modules\UsersAndTeams\Http\Requests\Team\ManageTeamMembersRequest;
use Modules\UsersAndTeams\Http\Requests\Team\StoreTeamRequest;
use Modules\UsersAndTeams\Http\Requests\Team\UpdateTeamRequest;
use Modules\UsersAndTeams\Models\Team;
use Modules\UsersAndTeams\Services\TeamService;

class TeamController extends Controller
{
    protected TeamService $teamService;

    /**
     * Summary of middleware
     * @return array<Middleware|string>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('role:Super Admin|Distribution Network Manager',
                            only:['index','store', 'show','update', 'destroy','assignMembers','removeMembers'
                            ]),
        ];
    }

    /**
     * Constructor for the TeamController class.
     * Initializes the $teamService property via dependency injection.
     *
     * @param TeamService $teamService
     */
    public function __construct(TeamService $teamService)
    {
        $this->teamService =$teamService;
    }

    /**
     * This method return all teams from database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'name']);
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->teamService->getAllTeams($filters)
                            ,200);
    }

    /**
     * Add a new team the database using the teamService via the createTeam method
     * passes the validated request data to createTeam.
     *
     * @param StoreTeamRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTeamRequest $request)
    {
        return $this->successResponse(
                            'Created succcessful'
                            ,$this->teamService->createTeam($request->validated())
                            ,201);
    }

    /**
     * Get team from database.
     * using the teamService via the showPipe method
     *
     * @param Team $team
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Team $team)
    {
        return $this->successResponse(
                            'Operation succcessful'
                            ,$this->teamService->showTeam($team)
                            ,200);
    }

    /**
     * Update a team in the database using the teamService via the updatePipe method.
     * passes the validated request data to updatePipe.
     *
     * @param UpdateTeamRequest $request
     * @param Team $team
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        return $this->successResponse(
                        'Updated succcessful'
                        ,$this->teamService->updateTeam($request->validated(),$team));
    }

    /**
     * Remove the specified team from database.
     *
     * @param Team $team
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Team $team)
    {
        $this->teamService->deleteTeam($team);
        return $this->successResponse(
                        'Deleted succcessful'
                        , null);
    }

    /**
     * Add a new members to the team
     *
     * @param Team $team
     * @param array $memberIds
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignMembers(ManageTeamMembersRequest $request, Team $team)
    {
        return $this->successResponse(
                        $this->teamService->assignTeamMembers($request->validated(),$team)
                        ,'Added members successfuly');
    }

    /**
     * Remove a new members to the team
     *
     * @param Team $team
     * @param array $memberIds
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeMembers(ManageTeamMembersRequest $request, Team $team)
    {
        return $this->successResponse(
                        $this->teamService->removeTeamMembers($request->validated(),$team)
                        ,'Removed members successfuly');
    }
}
