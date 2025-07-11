use pos_rpl;

select * from transaction_detail where product_id='PRD005';

SELECT 
    YEAR(created_at) AS year,
    MONTH(created_at) AS month,
    AVG(quantity)
FROM 
    transaction_detail
WHERE 
    YEAR(created_at) = 2020 AND product_id='PRD005'
GROUP BY 
    YEAR(created_at), MONTH(created_at)
ORDER BY 
    YEAR(created_at), MONTH(created_at);
