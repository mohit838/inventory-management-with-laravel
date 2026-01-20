import { z } from 'zod';

export const categorySchema = z.object({
    name: z.string().min(3, 'Name must be at least 3 characters'),
    description: z.string().optional(),
});

export type CategoryFormValues = z.infer<typeof categorySchema>;
