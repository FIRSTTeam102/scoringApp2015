
select * from alliance_results_v;

select * from alliance_pts_v;

select * from match_team_credits_v order by tournament_id, match_number, team_number;

select * from match_teams order by tournament_id, match_number, team_number;

create or replace view match_coop_v as
select mtc.tournament_id, mtc.match_number
  , max(mtc.coop_end_height) as coop_max_height
  , sum(mtc.coop_end_height - mtc.coop_start_height) as coop_num_totes
from match_team_cycles mtc
group by mtc.tournament_id, mtc.match_number;

create or replace view match_team_credits_v as
select mt.tournament_id, mt.match_number, mt.team_number
  , ifnull(sum((tote_end_height - tote_start_height) * 3), 0) as tote_credits
  , ifnull(sum((tote_end_height - tote_start_height) * 3 * case when container_scored = 'Y' then 1 else 0 end), 0) as container_credits
  , sum((case when litter_scored = 'Y' then 1 else 0 end) * 6) as litter_credits
from match_teams mt 
  left join match_team_cycles mtc on mtc.tournament_id = mt.tournament_id and mtc.match_number = mt.match_number
                                  and mtc.team_number = mt.team_number
group by mt.tournament_id, mt.match_number, mt.team_number;

create or replace view alliance_pts_v as
select
mt.tournament_id, mt.match_number, mt.alliance
, sum((case mt.auto_robot when 'Y' then 1 else 0 end)) AS `total_auto_robot`
, sum(auto_containers) total_auto_containers
, sum(auto_stack_totes) total_auto_stack_totes
, sum(auto_num_totes) total_auto_num_totes
, case when mt.alliance = 'RED' then red_score else blue_score end as alliance_score
, case when mcv.coop_max_height >= 4 then 'Y' else 'N' end as coop_stack_complete
, case when mcv.coop_num_totes >= 4 and mcv.coop_max_height < 4 then 'Y' else 'N' end as coop_set_complete
, sum(tote_credits + container_credits + litter_credits) total_teleop_credits
from match_teams mt, matches m
  , match_coop_v mcv
  , match_team_credits_v mtcv
where mt.tournament_id = m.tournament_id
and mt.match_number = m.match_number
and mcv.tournament_id = m.tournament_id
and mcv.match_number = m.match_number
and mtcv.tournament_id = mt.tournament_id
and mtcv.match_number = mt.match_number
and mtcv.team_number = mt.team_number
group by mt.tournament_id, mt.match_number, mt.alliance;

create or replace view alliance_results_v as
select  tournament_id, match_number, alliance
, case when total_auto_robot >= 3 then 'Y' else 'N' end as auto_robot_scored
, case when total_auto_stack_totes >= 3 then 'Y' else 'N' end as auto_stack_scored
, case when total_auto_num_totes >= 3 and total_auto_stack_totes < 3 then 'Y' else 'N' end as auto_tote_scored
, case when total_auto_containers >= 3 then 'Y' else 'N' end as auto_container_scored
, coop_stack_complete
, coop_set_complete
, total_auto_robot
, total_auto_stack_totes
, total_auto_num_totes
, total_auto_containers
, alliance_score
, case when total_auto_robot >= 3 then 4 else 0 end as auto_robot_pts
, case when total_auto_stack_totes >= 3 then 20 else 0 end as auto_stack_pts
, case when total_auto_num_totes >= 3 and total_auto_stack_totes < 3 then 6 else 0 end as auto_tote_pts
, case when total_auto_containers >= 3 then 8 else 0 end as auto_container_pts
, case when coop_set_complete = 'Y' then 20 else 0 end as coop_set_pts
, case when coop_stack_complete = 'Y' then 40 else 0 end as coop_stack_pts
, alliance_score 
  - (case when total_auto_robot >= 3 then 4 else 0 end)
  - (case when total_auto_stack_totes >= 3 then 20 else 0 end)
  - (case when total_auto_num_totes >= 3 and total_auto_stack_totes < 3 then 6 else 0 end)
  - (case when total_auto_containers >= 3 then 8 else 0 end)
  - (case when coop_set_complete = 'Y' then 20 else 0 end)
  - (case when coop_stack_complete = 'Y' then 40 else 0 end) teleop_pts
, total_teleop_credits
from alliance_pts_v;

create or replace view match_team_pts_v as
select mt.tournament_id, mt.match_number, mt.team_number, arv.alliance
, mtcv.tote_credits + mtcv.container_credits + mtcv.litter_credits as team_teleop_credits
, arv.total_teleop_credits
, arv.teleop_pts
, case when ifnull(arv.total_teleop_credits, 0) = 0 then null
    else ((mtcv.tote_credits + mtcv.container_credits + mtcv.litter_credits) * arv.teleop_pts) 
            / arv.total_teleop_credits end as team_teleop_pts
, (case when mt.auto_robot = 'Y' then 4/3 else 0 end)
    + (ifnull(mt.auto_num_totes, 0) 
      / (case when ifnull(arv.total_auto_num_totes, 0) = 0 then 1 else arv.total_auto_num_totes end) * 6)
    + (ifnull(mt.auto_stack_totes, 0)  
        / (case when ifnull(arv.total_auto_stack_totes, 0) = 0 then 1 else arv.total_auto_stack_totes end) * 20)
    + (case when ifnull(mt.auto_stack_totes, 0) = 0 then (ifnull(mt.auto_containers, 0) 
        / (case when ifnull(arv.total_auto_containers, 0) = 0 then 1 else arv.total_auto_containers end) * 8) else 0 end) 
          as team_auto_pts 
from match_teams mt
, match_team_credits_v mtcv
, alliance_results_v arv
where mt.tournament_id = mtcv.tournament_id
and mt.match_number = mtcv.match_number
and mt.team_number = mtcv.team_number
and mt.tournament_id = arv.tournament_id
and mt.match_number = arv.match_number
and mt.alliance = arv.alliance;

select * from match_team_pts_v
where tournament_id = 'T';

select mt.tournament_id, mt.match_number, mt.team_number
  , sum((tote_end_height - tote_start_height) * 3) as tote_credits
  , sum((tote_end_height - tote_start_height) * 3 * case when container_scored = 'Y' then 1 else 0 end) as container_credits
  , sum((case when litter_scored = 'Y' then 1 else 0 end) * 6) as litter_credits
from match_teams mt 
  left join match_team_cycles mtc on mtc.tournament_id = mt.tournament_id and mtc.match_number = mt.match_number
                                  and mtc.team_number = mt.team_number
group by mt.tournament_id, mt.match_number, mt.team_number
order by mt.tournament_id, mt.match_number, mt.team_number;

create or replace view match_team_results_v as 
select mt.tournament_id, `mt`.`match_number` AS `match_number`,`mt`.`team_number` AS `team_number`,`mt`.`alliance` AS `alliance`
,`mt`.`auto_containers` AS `auto_containers`,`mt`.`auto_robot` AS `auto_robot`,`mt`.`auto_stack_totes` AS `auto_stack_totes`
,`mt`.`auto_num_totes` AS `auto_num_totes`
,ifnull(sum((`mtc`.`tote_end_height` - `mtc`.`tote_start_height`)),0) AS `total_totes_stacked`
,sum((case `mtc`.`container_scored` when 'Y' then 1 else 0 end)) AS `total_containers_scored`
,sum((case `mtc`.`litter_scored` when 'Y' then 1 else 0 end)) AS `total_litter_scored`
,ifnull(sum((`mtc`.`coop_end_height` - `mtc`.`coop_start_height`)),0) AS `total_coop_totes_stacked`
,ifnull(`mt`.`foul_pts_against`,0) AS `foul_pts_against`
,(case `mt`.`alliance` when 'RED' then `m`.`red_score` else `m`.`blue_score` end) AS `score` 
, mtpv.team_teleop_pts
, mtpv.team_auto_pts
 , ifnull(least(sum(`mtc`.`coop_end_height` - `mtc`.`coop_start_height`), 3),0) / 4 * 40 as team_coop_stack_pts
 , ifnull(least(sum(case when `mtc`.`coop_end_height` = 1 then 1 else 0 end), 3),0) / 4 * 20 as team_coop_tote_pts
from ((((`match_teams` `mt` 
  left join `match_team_cycles` `mtc` 
    on(((`mtc`.`tournament_id` = `mt`.`tournament_id`) and (`mtc`.`match_number` = `mt`.`match_number`) 
      and (`mtc`.`team_number` = `mt`.`team_number`)))) 
join `tournaments` `t`) 
join `matches` `m`) 
join match_team_pts_v mtpv)
where ((`mt`.`tournament_id` = `t`.`ID`) and (`mt`.`match_number` = `m`.`match_number`) 
and (`m`.`tournament_id` = `t`.`ID`) 
and mtpv.tournament_id = mt.tournament_id
and mtpv.match_number = mt.match_number
and mtpv.team_number = mt.team_number
and (`mt`.`completed` = 'Y') 
) 
and m.ignore_match = 'N'
group by 1,2,3,4,5,6;

create or replace view team_avg_pts_v as
select Tournament_Id, Team_Number
, count(*) as "Num_Matches"
, round(sum(team_teleop_pts + team_auto_pts 
  + case when team_coop_stack_pts = 0 then team_coop_tote_pts else team_coop_stack_pts end) / count(*), 3) as "Avg_Earned_Pts_Per_Match"
, round(sum(team_teleop_pts) / count(*), 3) as "Avg_Earned_Teleop_Pts_Per_Match"
, round(sum(team_auto_pts) / count(*), 3) as "Avg_Earned_Auto_Pts_Per_Match"
, round(sum(case when team_coop_stack_pts = 0 then team_coop_tote_pts else team_coop_stack_pts end) / count(*), 3) as "Avg_Earned_Coop_Pts_Per_Match"
, round(sum(score) / count(*), 3) as "Avg_Pts_Per_Match"
, sum(score) as "Total_Pts"
, round(sum(team_teleop_pts + team_auto_pts 
    + case when team_coop_stack_pts = 0 then team_coop_tote_pts else team_coop_stack_pts end), 1) as "Total_Earned_Pts"
, round((sum(score) / count(*) / 3) - (sum(team_teleop_pts + team_auto_pts 
  + case when team_coop_stack_pts = 0 then team_coop_tote_pts else team_coop_stack_pts end) / count(*)), 3) as "Luck_Factor"
from match_team_results_v
group by tournament_id, team_number;

select * from team_avg_pts_v
where tournament_id = 'T';

select * from match_team_results_v
where tournament_id = 'T';

select * from match_teams
where tournament_id = 'T'
and completed = 'Y';

select * from match_team_pts_v
where tournament_id = 'T';

select * from alliance_results_v
where tournament_id = 'T';

select * from match_coop_v
where tournament_id = 'T';

select * from match_coop_v
where tournament_id = 'T';

select * from match_coop_v;

select *
from alliance_results_v arv
, match_team_pts_v mtpv
where arv.tournament_id = mtpv.tour;

select * from match_teams
where tournament_id = 'T';

select * from match_team_credits_v
where tournament_id = 'UM'
and team_number = 102;

select * from alliance_pts_v
where tournament_id = 'UM';

select * from alliance_results_v
where tournament_id = 'UM';

select * from match_team_results_v
where tournament_id = 'UM';

select * from match_team_pts_v
where tournament_id = 'UM'
and team_number = 102;

select * from match_teams
where tournament_id = 'UM'
and match_number = 13
order by match_number, team_number;