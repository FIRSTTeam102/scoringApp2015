select * from match_teams;

update match_teams set completed = 'N'
where completed = 'Y';



select mt.tournament_id, mt.team_number, count(mt.match_number) num_matches
, avg(mtc.tote_end_height - mtc.tote_start_height) as avgAdded
, sum(CASE container_scored
            WHEN 'Y' THEN 1
            ELSE 0
        END) AS `total_containers`
, sum(CASE mtc.litter_scored
            WHEN 'Y' THEN 1
            ELSE 0
        END) AS `total_litter`
, avg(mtc.coop_end_height - mtc.coop_start_height) as avgCoopAdded
FROM ((`match_teams` `mt`
       LEFT JOIN `match_team_cycles` `mtc` on(((`mtc`.`tournament_id` = `mt`.`tournament_id`)
                                               AND (`mtc`.`match_number` = `mt`.`match_number`)
                                               AND (`mtc`.`team_number` = `mt`.`team_number`))))
      JOIN `tournaments` `t`)
WHERE ((`mt`.`tournament_id` = `t`.`ID`)
       AND (`mt`.`completed` = 'Y')
       AND (`t`.`active` = 'Y'))
group by 1, 2
;

select * 
from match_team_cycles
;