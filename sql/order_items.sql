CREATE TABLE order_items (
    ono INT NOT NULL,
    pno INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (ono, pno),
    FOREIGN KEY (ono) REFERENCES orders(ono),
    FOREIGN KEY (pno) REFERENCES products(pno)
);